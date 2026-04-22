<?php
include 'config.php';
include 'workflow_helper.php';
checkLogin();
checkRole(['user']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$po_id = isset($_POST['po_id']) ? (int)$_POST['po_id'] : 0;

$checkStmt = $conn->prepare("SELECT po_status FROM purchase_orders WHERE id = ?");
$checkStmt->bind_param("i", $po_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$po = $checkResult->fetch_assoc();

if (!$po) {
    die("PO not found.");
}

if ($po['po_status'] === 'done' || $po['po_status'] === 'sent_to_schedule_delivery' || $po['po_status'] === 'delivery_date_scheduled') {
    die("This PO cannot be changed.");
}

$expected_delivery_date = !empty($_POST['expected_delivery_date']) ? $_POST['expected_delivery_date'] : NULL;
$user_id = $_SESSION['user_id'];

$poStmt = $conn->prepare("UPDATE purchase_orders 
                          SET expected_delivery_date = ?, 
                              po_status = 'sent_to_schedule_delivery'
                          WHERE id = ?");
$poStmt->bind_param("si", $expected_delivery_date, $po_id);
$poStmt->execute();

savePoWorkflow(
    $conn,
    $po_id,
    'Expected Delivery Saved',
    'sent_to_schedule_delivery',
    'Expected delivery date set to ' . $expected_delivery_date,
    $user_id
);

header("Location: po_view.php?id=" . $po_id);
exit();
?>