<?php
include 'config.php';
checkLogin();

$po_id = isset($_POST['po_id']) ? (int)$_POST['po_id'] : 0;
$deliverableQtyList = $_POST['deliverable_qty'] ?? [];
$reason = trim($_POST['reason'] ?? '');
$updated_by = $_SESSION['user_id'];

foreach ($deliverableQtyList as $item_id => $deliverable_qty) {

    $item_id = (int)$item_id;
    $deliverable_qty = (int)$deliverable_qty;

    $stmt = $conn->prepare("SELECT qty FROM po_items WHERE id = ? AND po_id = ?");
    $stmt->bind_param("ii", $item_id, $po_id);
    $stmt->execute();

    $item = $stmt->get_result()->fetch_assoc();

    if (!$item) {
        die("Invalid item.");
    }

    $po_qty = (int)$item['qty'];

    if ($deliverable_qty > $po_qty) {
        die("Deliverable quantity cannot be greater than PO quantity.");
    }

    if ($deliverable_qty == $po_qty) {
        $status = "full";
        $item_reason = null;
    } elseif ($deliverable_qty > 0) {
        $status = "partial";
        $item_reason = $reason;
    } else {
        $status = "cannot";
        $item_reason = $reason;
    }

    $update = $conn->prepare("
        UPDATE po_items 
        SET 
            deliverable_qty = ?,
            user_status = ?,
            reason = ?,
            updated_by = ?,
            updated_at = NOW()
        WHERE id = ? AND po_id = ?
    ");

    $update->bind_param(
        "issiii",
        $deliverable_qty,
        $status,
        $item_reason,
        $updated_by,
        $item_id,
        $po_id
    );

    $update->execute();
}

header("Location: po_view.php?id=" . $po_id . "&updated=1");
exit();
?>