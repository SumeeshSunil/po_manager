<?php
include 'config.php';
include 'workflow_helper.php';
checkLogin();
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$po_id = isset($_POST['po_id']) ? (int)$_POST['po_id'] : 0;
$reschedule_date = isset($_POST['reschedule_date']) ? trim($_POST['reschedule_date']) : '';

if ($po_id <= 0) {
    die("Invalid PO ID.");
}

if ($reschedule_date === '') {
    die("Reschedule date is required.");
}

$checkStmt = $conn->prepare("SELECT po_status FROM purchase_orders WHERE id = ?");
$checkStmt->bind_param("i", $po_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$po = $checkResult->fetch_assoc();

if (!$po) {
    die("PO not found.");
}

if (!in_array($po['po_status'], ['delivery_date_scheduled', 'rejected'])) {
    die("Only scheduled or rejected POs can be rescheduled.");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE purchase_orders 
                        SET 
                            po_status = 'delivery_date_scheduled',
                            delivery_schedule_date = ?,
                            reschedule_date = ?
                        WHERE id = ?");
$stmt->bind_param("ssi", $reschedule_date, $reschedule_date, $po_id);
$stmt->execute();

savePoWorkflow(
    $conn,
    $po_id,
    'PO Rescheduled',
    'delivery_date_scheduled',
    'Delivery rescheduled to: ' . $reschedule_date,
    $user_id
);

header("Location: dashboard.php");
exit();
?>