<?php
include 'config.php';
checkLogin();

header('Content-Type: application/json');

$sql = "SELECT po.*, u.name AS creator_name
        FROM purchase_orders po
        LEFT JOIN users u ON po.created_by = u.id
        ORDER BY po.id DESC";

$result = $conn->query($sql);

$rows = [];
while ($row = $result->fetch_assoc()) {
    $st = strtolower($row['po_status'] ?? '');

    $row['can_mark_done'] = (
        isset($_SESSION['role']) &&
        $_SESSION['role'] === 'admin' &&
        $st === 'delivery_date_scheduled'
    );

    $rows[] = [
        'id'                     => (int)$row['id'],
        'po_number'              => $row['po_number'] ?? '',
        'platform'               => $row['platform'] ?? '',
        'factory_name'           => $row['factory_name'] ?? '',
        'release_date'           => $row['release_date'] ?? '',
        'expiry_date'            => $row['expiry_date'] ?? '',
        'po_status'              => $row['po_status'] ?? '',
        'expected_delivery_date' => $row['expected_delivery_date'] ?? '',
        'delivery_schedule_date' => $row['delivery_schedule_date'] ?? '',
        'creator_name'           => $row['creator_name'] ?? '',
        'pdf_file_path'          => $row['pdf_file_path'] ?? '',
        'can_mark_done'          => $row['can_mark_done']
    ];
}

$total      = count($rows);
$done       = count(array_filter($rows, fn($r) => strtolower($r['po_status']) === 'done'));
$scheduled  = count(array_filter($rows, fn($r) => strtolower($r['po_status']) === 'delivery_date_scheduled'));
$needsSched = count(array_filter($rows, fn($r) => strtolower($r['po_status']) === 'sent_to_schedule_delivery'));
$open       = $total - $done;

echo json_encode([
    'success' => true,
    'rows'    => $rows,
    'stats'   => [
        'total'          => $total,
        'open'           => $open,
        'needs_schedule' => $needsSched,
        'scheduled'      => $scheduled,
        'done'           => $done
    ]
]);