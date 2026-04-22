<?php
include 'partials/header.php';
checkLogin();

$po_id = isset($_GET['po_id']) ? (int)$_GET['po_id'] : 0;

if ($po_id <= 0) {
    die("Invalid PO ID.");
}

$poStmt = $conn->prepare("SELECT po_number FROM purchase_orders WHERE id = ?");
$poStmt->bind_param("i", $po_id);
$poStmt->execute();
$poResult = $poStmt->get_result();
$po = $poResult->fetch_assoc();

if (!$po) {
    die("PO not found.");
}

$stmt = $conn->prepare("SELECT h.*, u.name AS done_by_name
                        FROM po_workflow_history h
                        LEFT JOIN users u ON h.done_by = u.id
                        WHERE h.po_id = ?
                        ORDER BY h.done_at DESC");
$stmt->bind_param("i", $po_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  .history-page {
    min-height: 100vh;
    background: #f0f2f5;
    padding: 32px 24px 60px;
    font-family: 'DM Sans', sans-serif;
  }

  .history-card {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  }

  .history-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e8eaed;
    background: #fafafa;
  }

  .history-header h2 {
    font-size: 22px;
    color: #1a1a2e;
    margin-bottom: 5px;
  }

  .history-header p {
    font-size: 13px;
    color: #777;
  }

  .history-table {
    width: 100%;
    border-collapse: collapse;
  }

  .history-table th, .history-table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #f0f2f5;
    font-size: 13px;
  }

  .history-table th {
    background: #fafafa;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #888;
  }

  .history-table tr:hover {
    background: #fafbfc;
  }

  .back-btn {
    display: inline-block;
    margin-bottom: 18px;
    text-decoration: none;
    background: #1a1a2e;
    color: #fff;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
  }

  .empty {
    padding: 30px;
    text-align: center;
    color: #999;
  }
</style>

<div class="history-page">
  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

  <div class="history-card">
    <div class="history-header">
      <h2>PO Workflow History</h2>
      <p>Purchase Order: <?php echo htmlspecialchars($po['po_number']); ?></p>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <table class="history-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Action</th>
            <th>Status</th>
            <th>Note</th>
            <th>Done By</th>
            <th>Date & Time</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($row['action_type']); ?></td>
              <td><?php echo htmlspecialchars(str_replace('_', ' ', $row['status_value'])); ?></td>
              <td><?php echo htmlspecialchars($row['action_note'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($row['done_by_name'] ?? '—'); ?></td>
              <td><?php echo date('d-m-Y h:i A', strtotime($row['done_at'])); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty">No workflow history found for this PO.</div>
    <?php endif; ?>
  </div>
</div>

<?php include 'partials/footer.php'; ?>