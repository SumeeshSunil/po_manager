<?php
include 'partials/header.php';
checkLogin();
checkRole(['super', 'admin']);

$item_code = isset($_GET['item_code']) ? trim($_GET['item_code']) : '';
if ($item_code == '') die("Invalid item code");

$stmt = $conn->prepare("
    SELECT 
        pi.item_code,
        pi.item_description,
        po.po_number,
        po.factory_name,
        po.platform,
        po.po_status,
        pi.qty AS initial_qty,
        COALESCE(pi.deliverable_qty, 0) AS updated_qty,
        CASE 
            WHEN COALESCE(pi.deliverable_qty, 0) < pi.qty 
            THEN pi.qty - COALESCE(pi.deliverable_qty, 0)
            ELSE 0
        END AS remaining_qty,
        pi.user_status,
        pi.expected_delivery_date,
        pi.reason,
        u.name AS updated_by_name
    FROM po_items pi
    INNER JOIN purchase_orders po ON po.id = pi.po_id
    LEFT JOIN users u ON pi.updated_by = u.id
    WHERE po.po_status != 'done'
      AND pi.item_code = ?
    ORDER BY po.factory_name ASC, po.po_number ASC
");
$stmt->bind_param("s", $item_code);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) $rows[] = $row;

if (empty($rows)) {
    echo "<p>No open pending records found for this item.</p>";
    include 'partials/footer.php';
    exit();
}

$firstRow       = $rows[0];
$totalInitial   = array_sum(array_column($rows, 'initial_qty'));
$totalDelivered = array_sum(array_column($rows, 'updated_qty'));
$totalRemaining = array_sum(array_column($rows, 'remaining_qty'));
$pct = $totalInitial > 0 ? round(($totalDelivered / $totalInitial) * 100) : 0;
?>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  .detail-page {
    min-height: 100vh;
    background: #f0f2f5;
    padding: 32px 24px 60px;
    font-family: 'DM Sans', sans-serif;
  }

  /* Back link */
  .back-link {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 500; color: #888;
    text-decoration: none; margin-bottom: 20px;
    transition: color 0.15s;
  }
  .back-link:hover { color: #1a1a2e; }
  .back-link svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }

  /* Page header */
  .page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    margin-bottom: 24px; gap: 16px; flex-wrap: wrap;
  }

  .page-header-left { display: flex; align-items: center; gap: 14px; }

  .page-header-icon {
    width: 48px; height: 48px; background: #1a1a2e;
    border-radius: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .page-header-icon svg { width: 24px; height: 24px; stroke: #fff; fill: none; stroke-width: 1.8; }

  .page-header h2 { font-size: 20px; font-weight: 700; color: #1a1a2e; letter-spacing: -0.3px; }
  .page-header .item-code-big {
    font-family: 'DM Mono', monospace; font-size: 13px; font-weight: 500;
    background: #f0f2f5; color: #555; padding: 3px 10px; border-radius: 7px;
    display: inline-block; margin-top: 5px;
  }

  /* Stats */
  .stats-row {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 14px; margin-bottom: 24px;
  }

  .stat-card {
    background: #fff; border: 1px solid #e8eaed;
    border-radius: 14px; padding: 18px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  }
  .stat-card .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: #999; margin-bottom: 6px; }
  .stat-card .stat-value { font-size: 26px; font-weight: 700; color: #1a1a2e; font-family: 'DM Mono', monospace; line-height: 1; }
  .stat-card .stat-sub { font-size: 12px; color: #aaa; margin-top: 4px; }

  /* Progress card */
  .progress-card {
    background: #fff; border: 1px solid #e8eaed;
    border-radius: 14px; padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
  }

  .progress-card .prog-label { font-size: 13px; font-weight: 600; color: #1a1a2e; white-space: nowrap; }
  .progress-card .prog-pct {
    font-size: 22px; font-weight: 700; font-family: 'DM Mono', monospace;
    color: #1a1a2e; min-width: 52px;
  }
  .prog-bar-wrap { flex: 1; height: 8px; background: #e8eaed; border-radius: 99px; overflow: hidden; min-width: 120px; }
  .prog-bar { height: 100%; border-radius: 99px; transition: width 0.6s ease; }

  /* Table card */
  .table-card {
    background: #fff; border: 1px solid #e8eaed;
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  }

  .table-card-header {
    padding: 16px 24px; background: #fafafa;
    border-bottom: 1px solid #e8eaed;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
  }

  .table-card-header-left { display: flex; align-items: center; gap: 10px; }
  .section-dot { width: 8px; height: 8px; border-radius: 50%; background: #1a1a2e; flex-shrink: 0; }
  .table-card-header span { font-size: 13px; font-weight: 600; color: #1a1a2e; text-transform: uppercase; letter-spacing: 0.6px; }

  .row-count {
    font-size: 12px; color: #aaa; font-weight: 500;
    background: #f0f2f5; padding: 3px 10px; border-radius: 20px;
  }

  /* Table */
  .detail-table { width: 100%; border-collapse: collapse; }

  .detail-table th {
    padding: 11px 16px; font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.5px; color: #888;
    background: #fafafa; border-bottom: 1px solid #e8eaed;
    text-align: left; white-space: nowrap;
  }

  .detail-table td {
    padding: 13px 16px; font-size: 13px; color: #333;
    border-bottom: 1px solid #f0f2f5; vertical-align: middle;
  }

  .detail-table tr:last-child td { border-bottom: none; }
  .detail-table tbody tr { transition: background 0.12s; }
  .detail-table tbody tr:hover { background: #fafbfc; }

  .po-num {
    font-family: 'DM Mono', monospace; font-size: 12px; font-weight: 500;
    color: #1a1a2e; background: #f0f2f5; padding: 3px 8px; border-radius: 6px;
  }

  /* Platform badge */
  .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
  .badge-instamart { background: #fff3e0; color: #e65100; }
  .badge-blinkit   { background: #f9fbe7; color: #827717; }
  .badge-zepto     { background: #fce4ec; color: #880e4f; }
  .badge-flipkart  { background: #e3f2fd; color: #0d47a1; }
  .badge-default   { background: #f0f2f5; color: #555; }

  /* PO status */
  .status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600;
  }
  .status-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
  .status-open    { background: #e8f5e9; color: #2e7d32; }
  .status-open .dot { background: #43a047; }
  .status-pending { background: #fff8e1; color: #f57f17; }
  .status-pending .dot { background: #ffb300; }
  .status-other   { background: #f0f2f5; color: #666; }
  .status-other .dot { background: #bbb; }

  /* User status badge */
  .user-status {
    display: inline-block; padding: 3px 10px; border-radius: 6px;
    font-size: 11px; font-weight: 600; background: #f0f2f5; color: #555;
  }

  /* Remaining pill */
  .remaining-pill {
    display: inline-flex; align-items: center; gap: 4px;
    background: #ffebee; color: #c62828;
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 700; font-family: 'DM Mono', monospace;
  }

  .zero-pill {
    background: #e8f5e9; color: #2e7d32;
  }

  /* Qty numbers */
  .qty-num { font-family: 'DM Mono', monospace; font-size: 13px; font-weight: 600; }

  /* Reason text */
  .reason-text {
    max-width: 180px; font-size: 12px; color: #666;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }

  .na-text { color: #ccc; font-size: 12px; }
</style>

<div class="detail-page">

  <a href="pending_items.php" class="back-link">
    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Pending Items
  </a>

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
      </div>
      <div>
        <h2><?php echo htmlspecialchars($firstRow['item_description']); ?></h2>
        <span class="item-code-big"><?php echo htmlspecialchars($firstRow['item_code']); ?></span>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-label">Open POs</div>
      <div class="stat-value"><?php echo count($rows); ?></div>
      <div class="stat-sub">With this item</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Ordered</div>
      <div class="stat-value"><?php echo number_format($totalInitial); ?></div>
      <div class="stat-sub">Units</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Delivered</div>
      <div class="stat-value" style="color:#2e7d32"><?php echo number_format($totalDelivered); ?></div>
      <div class="stat-sub">Units</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Remaining</div>
      <div class="stat-value" style="color:#c62828"><?php echo number_format($totalRemaining); ?></div>
      <div class="stat-sub">Units</div>
    </div>
  </div>

  <!-- Progress bar -->
  <?php
    $barColor = $pct >= 80 ? '#43a047' : ($pct >= 40 ? '#ffb300' : '#e53935');
  ?>
  <div class="progress-card">
    <div class="prog-label">Overall Fulfillment</div>
    <div class="prog-pct"><?php echo $pct; ?>%</div>
    <div class="prog-bar-wrap">
      <div class="prog-bar" style="width:<?php echo $pct; ?>%; background:<?php echo $barColor; ?>"></div>
    </div>
  </div>

  <!-- Table -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-header-left">
        <div class="section-dot"></div>
        <span>PO Breakdown</span>
      </div>
      <span class="row-count"><?php echo count($rows); ?> record(s)</span>
    </div>

    <div style="overflow-x:auto">
      <table class="detail-table">
        <thead>
          <tr>
            <th>PO Number</th>
            <th>Factory</th>
            <th>Platform</th>
            <th>PO Status</th>
            <th>Ordered</th>
            <th>Delivered</th>
            <th>Remaining</th>
            <th>User Status</th>
            <th>Expected Delivery</th>
            <th>Reason</th>
            <th>Updated By</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row):
            $platform = strtolower($row['platform'] ?? '');
            $platformClass = match($platform) {
              'instamart' => 'badge-instamart',
              'blinkit'   => 'badge-blinkit',
              'zepto'     => 'badge-zepto',
              'flipkart'  => 'badge-flipkart',
              default     => 'badge-default',
            };
            $poStatus = strtolower($row['po_status'] ?? '');
            $statusClass = match($poStatus) {
              'open'    => 'status-open',
              'pending' => 'status-pending',
              default   => 'status-other',
            };
          ?>
            <tr>
              <td><span class="po-num"><?php echo htmlspecialchars($row['po_number']); ?></span></td>
              <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap" title="<?php echo htmlspecialchars($row['factory_name']); ?>"><?php echo htmlspecialchars($row['factory_name']); ?></td>
              <td><span class="badge <?php echo $platformClass; ?>"><?php echo htmlspecialchars($row['platform'] ?? '—'); ?></span></td>
              <td>
                <span class="status-badge <?php echo $statusClass; ?>">
                  <span class="dot"></span>
                  <?php echo ucfirst($row['po_status'] ?? '—'); ?>
                </span>
              </td>
              <td><span class="qty-num"><?php echo number_format($row['initial_qty']); ?></span></td>
              <td><span class="qty-num" style="color:#2e7d32"><?php echo number_format($row['updated_qty']); ?></span></td>
              <td>
                <?php if ($row['remaining_qty'] > 0): ?>
                  <span class="remaining-pill">+<?php echo number_format($row['remaining_qty']); ?></span>
                <?php else: ?>
                  <span class="remaining-pill zero-pill">✓ Done</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($row['user_status'])): ?>
                  <span class="user-status"><?php echo htmlspecialchars($row['user_status']); ?></span>
                <?php else: ?>
                  <span class="na-text">—</span>
                <?php endif; ?>
              </td>
              <td style="font-size:12px; color:#666"><?php echo $row['expected_delivery_date'] ?? '<span class="na-text">—</span>'; ?></td>
              <td>
                <?php if (!empty($row['reason'])): ?>
                  <span class="reason-text" title="<?php echo htmlspecialchars($row['reason']); ?>"><?php echo htmlspecialchars($row['reason']); ?></span>
                <?php else: ?>
                  <span class="na-text">—</span>
                <?php endif; ?>
              </td>
              <td style="font-size:12px; color:#666"><?php echo htmlspecialchars($row['updated_by_name'] ?? '—'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include 'partials/footer.php'; ?>