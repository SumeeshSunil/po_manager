<?php
include 'partials/header.php';
checkLogin();

$sql = "SELECT po.*, u.name AS creator_name
        FROM purchase_orders po
        LEFT JOIN users u ON po.created_by = u.id
        ORDER BY po.id DESC";
$result = $conn->query($sql);
?>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .dash-page {
        min-height: 100vh;
        background: #f0f2f5;
        padding: 32px 24px 60px;
        font-family: 'DM Sans', sans-serif;
    }

    .dash-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .dash-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .dash-header-icon {
        width: 44px;
        height: 44px;
        background: #1a1a2e;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .dash-header-icon svg {
        width: 22px;
        height: 22px;
        stroke: #fff;
        fill: none;
        stroke-width: 1.8;
    }

    .dash-header h2 {
        font-size: 22px;
        font-weight: 600;
        color: #1a1a2e;
        letter-spacing: -0.3px;
    }

    .dash-header p {
        font-size: 13px;
        color: #888;
        margin-top: 2px;
    }

    .btn-new {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        background: #1a1a2e;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        text-decoration: none;
        transition: background 0.15s;
        cursor: pointer;
    }

    .btn-new:hover {
        background: #2d2d4e;
    }

    .btn-new svg {
        width: 15px;
        height: 15px;
        stroke: #fff;
        fill: none;
        stroke-width: 2.5;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .stat-card .stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #999;
        margin-bottom: 6px;
    }

    .stat-card .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #1a1a2e;
        font-family: 'DM Mono', monospace;
        line-height: 1;
    }

    .stat-card .stat-sub {
        font-size: 12px;
        color: #aaa;
        margin-top: 4px;
    }

    .table-card {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .table-card-header {
        padding: 16px 24px;
        background: #fafafa;
        border-bottom: 1px solid #e8eaed;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-card-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #1a1a2e;
        flex-shrink: 0;
    }

    .table-card-header span {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a2e;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f0f2f5;
        border: 1.5px solid #e0e3e8;
        border-radius: 8px;
        padding: 6px 12px;
    }

    .search-box svg {
        width: 14px;
        height: 14px;
        stroke: #aaa;
        fill: none;
        stroke-width: 2;
        flex-shrink: 0;
    }

    .search-box input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        color: #1a1a2e;
        width: 180px;
    }

    .search-box input::placeholder {
        color: #bbb;
    }

    .po-table {
        width: 100%;
        border-collapse: collapse;
    }

    .po-table th {
        padding: 11px 16px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        background: #fafafa;
        border-bottom: 1px solid #e8eaed;
        text-align: left;
        white-space: nowrap;
    }

    .po-table td {
        padding: 13px 16px;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #f0f2f5;
        vertical-align: middle;
    }

    .po-table tr:last-child td {
        border-bottom: none;
    }

    .po-table tbody tr {
        transition: background 0.12s;
    }

    .po-table tbody tr:hover {
        background: #fafbfc;
    }

    .po-num {
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        font-weight: 500;
        color: #1a1a2e;
        background: #f0f2f5;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .badge-instamart {
        background: #fff3e0;
        color: #e65100;
    }

    .badge-blinkit {
        background: #f9fbe7;
        color: #827717;
    }

    .badge-zepto {
        background: #fce4ec;
        color: #880e4f;
    }

    .badge-flipkart {
        background: #e3f2fd;
        color: #0d47a1;
    }

    .badge-default {
        background: #f0f2f5;
        color: #555;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-pending {
        background: #fff8e1;
        color: #f57f17;
    }

    .status-pending .dot {
        background: #ffb300;
    }

    .status-in_progress {
        background: #e3f2fd;
        color: #1565c0;
    }

    .status-in_progress .dot {
        background: #1e88e5;
    }

    .status-sent_to_schedule_delivery {
        background: #fff3e0;
        color: #ef6c00;
    }

    .status-sent_to_schedule_delivery .dot {
        background: #fb8c00;
    }

    .status-delivery_date_scheduled {
        background: #ede7f6;
        color: #6a1b9a;
    }

    .status-delivery_date_scheduled .dot {
        background: #8e24aa;
    }

    .status-done {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-done .dot {
        background: #43a047;
    }

    .status-other {
        background: #f0f2f5;
        color: #666;
    }

    .status-other .dot {
        background: #bbb;
    }

    .schedule-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #e8f5e9;
        color: #2e7d32;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        font-family: 'DM Mono', monospace;
    }

    .schedule-pill svg {
        width: 11px;
        height: 11px;
        stroke: #2e7d32;
        fill: none;
        stroke-width: 2.5;
    }

    .needs-schedule {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fff3e0;
        color: #e65100;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .needs-schedule svg {
        width: 11px;
        height: 11px;
        stroke: #e65100;
        fill: none;
        stroke-width: 2.5;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-link,
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s;
        border: none;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }

    .action-link svg,
    .action-btn svg {
        width: 13px;
        height: 13px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .action-view {
        color: #1a1a2e;
        background: #f0f2f5;
    }

    .action-view:hover {
        background: #e4e7ec;
    }

    .action-pdf {
        color: #c62828;
        background: #ffebee;
    }

    .action-pdf:hover {
        background: #ffcdd2;
    }

    .action-done {
        color: #fff;
        background: #2e7d32;
    }

    .action-done:hover {
        background: #1b5e20;
    }

    .no-pdf {
        font-size: 12px;
        color: #bbb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 24px;
        color: #aaa;
        font-size: 14px;
    }

    .empty-state svg {
        width: 40px;
        height: 40px;
        stroke: #ddd;
        fill: none;
        stroke-width: 1.5;
        margin-bottom: 12px;
    }
</style>

<div class="dash-page">

    <div class="dash-header">
        <div class="dash-header-left">
            <div class="dash-header-icon">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
            </div>
            <div>
                <h2>Purchase Orders</h2>
                <p>Manage and track all incoming POs</p>
            </div>
        </div>
        <a href="create_po.php" class="btn-new">
            <svg viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            New PO
        </a>
    </div>

    <?php
    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;

    $total = count($rows);
    $done = count(array_filter($rows, fn($r) => strtolower($r['po_status'] ?? '') === 'done'));
    $scheduled = count(array_filter($rows, fn($r) => strtolower($r['po_status'] ?? '') === 'delivery_date_scheduled'));
    $needsSched = count(array_filter($rows, fn($r) => strtolower($r['po_status'] ?? '') === 'sent_to_schedule_delivery'));
    $open = $total - $done;
    ?>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total POs</div>
            <div class="stat-value"><?php echo $total; ?></div>
            <div class="stat-sub">All time</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Open</div>
            <div class="stat-value" style="color:#1565c0"><?php echo $open; ?></div>
            <div class="stat-sub">Active items</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Needs Schedule</div>
            <div class="stat-value" style="color:#ef6c00"><?php echo $needsSched; ?></div>
            <div class="stat-sub">Waiting for schedule date</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Scheduled</div>
            <div class="stat-value" style="color:#8e24aa"><?php echo $scheduled; ?></div>
            <div class="stat-sub">Delivery date set</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Done</div>
            <div class="stat-value" style="color:#2e7d32"><?php echo $done; ?></div>
            <div class="stat-sub">Completed</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-header-left">
                <div class="section-dot"></div>
                <span>All Purchase Orders</span>
            </div>
            <div class="search-box">
                <svg viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" id="search-input" placeholder="Search POs...">
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="po-table" id="po-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>PO Number</th>
                        <th>Platform</th>
                        <th>Factory</th>
                        <th>Release Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Expected Delivery</th>
                        <th>Schedule Date</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                    <div>No purchase orders yet</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <?php
                            $platform = strtolower($row['platform'] ?? '');
                            $platformClass = match ($platform) {
                                'instamart' => 'badge-instamart',
                                'blinkit'   => 'badge-blinkit',
                                'zepto'     => 'badge-zepto',
                                'flipkart'  => 'badge-flipkart',
                                default     => 'badge-default',
                            };

                            $status = strtolower($row['po_status'] ?? '');
                            $statusClass = match ($status) {
                                'pending' => 'status-pending',
                                'in_progress' => 'status-in_progress',
                                'sent_to_schedule_delivery' => 'status-sent_to_schedule_delivery',
                                'delivery_date_scheduled' => 'status-delivery_date_scheduled',
                                'done' => 'status-done',
                                default => 'status-other',
                            };

                            $showExpected = in_array($status, ['sent_to_schedule_delivery', 'delivery_date_scheduled', 'done']);
                            $showSchedule = in_array($status, ['delivery_date_scheduled', 'done']);
                            $canMarkDone = ($_SESSION['role'] === 'admin' && $status === 'delivery_date_scheduled');
                            ?>
                            <tr>
                                <td style="color:#bbb; font-size:12px; font-family:'DM Mono',monospace"><?php echo $row['id']; ?></td>
                                <td><span class="po-num"><?php echo htmlspecialchars($row['po_number']); ?></span></td>
                                <td><span class="badge <?php echo $platformClass; ?>"><?php echo htmlspecialchars($row['platform'] ?? '—'); ?></span></td>
                                <td style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap" title="<?php echo htmlspecialchars($row['factory_name'] ?? ''); ?>"><?php echo htmlspecialchars($row['factory_name'] ?? '—'); ?></td>
                                <td style="font-size:12px; color:#666"><?php echo !empty($row['release_date']) ? date('d-m-Y', strtotime($row['release_date'])) : '—'; ?></td>
                                <td style="font-size:12px; color:#666"><?php echo !empty($row['expiry_date']) ? date('d-m-Y', strtotime($row['expiry_date'])) : '—'; ?></td>

                                <td>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <span class="dot"></span>
                                        <?php echo ucfirst(str_replace('_', ' ', $row['po_status'] ?? 'N/A')); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($showExpected && !empty($row['expected_delivery_date'])): ?>
                                        <span class="schedule-pill">
                                            <svg viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <?php echo date('d-m-Y', strtotime($row['expected_delivery_date'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:#ccc; font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($showSchedule): ?>
                                        <?php if (!empty($row['delivery_schedule_date'])): ?>
                                            <span class="schedule-pill">
                                                <svg viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <?php echo date('d-m-Y', strtotime($row['delivery_schedule_date'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="needs-schedule">
                                                <svg viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="12" y1="8" x2="12" y2="12" />
                                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                                </svg>
                                                Not scheduled
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color:#ccc; font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>

                                <td style="font-size:12px; color:#666"><?php echo htmlspecialchars($row['creator_name'] ?? '—'); ?></td>

                                <td>
                                    <div class="action-group">
                                        <a href="po_view.php?id=<?php echo $row['id']; ?>" class="action-link action-view">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            View
                                        </a>

                                        <?php if (!empty($row['pdf_file_path'])): ?>
                                            <a href="<?php echo htmlspecialchars($row['pdf_file_path']); ?>" target="_blank" class="action-link action-pdf">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                    <polyline points="14 2 14 8 20 8" />
                                                </svg>
                                                PDF
                                            </a>
                                        <?php else: ?>
                                            <span class="no-pdf">No PDF</span>
                                        <?php endif; ?>

                                        <?php if ($canMarkDone): ?>
                                            <form method="POST" action="mark_po_done.php" onsubmit="return confirm('Are you sure you want to mark this PO as done?');" style="display:inline;">
                                                <input type="hidden" name="po_id" value="<?php echo (int)$row['id']; ?>">
                                                <button type="submit" class="action-btn action-done">
                                                    <svg viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Mark Done
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <a href="po_workflow_history.php?po_id=<?php echo $row['id']; ?>" class="action-link action-view">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M12 8v4l3 3" />
                                                <circle cx="12" cy="12" r="9" />
                                            </svg>
                                            History
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.getElementById('search-input').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#po-table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

<?php include 'partials/footer.php'; ?>