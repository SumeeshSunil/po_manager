<?php
if (!isset($total)) {
    $total = 0;
}
if (!isset($done)) {
    $done = 0;
}

$doneRate = ($total > 0) ? round(($done / $total) * 100, 1) : 0;
?>

<style>
.po-rate-card {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 16px;
    padding: 18px 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.po-rate-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.po-rate-title {
    font-size: 13px;
    font-weight: 600;
    color: #1a1a2e;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.po-rate-value {
    font-size: 22px;
    font-weight: 700;
    color: #2e7d32;
    font-family: 'DM Mono', monospace;
}

.po-rate-meta {
    font-size: 12px;
    color: #888;
    margin-bottom: 12px;
}

.po-rate-track {
    width: 100%;
    height: 14px;
    background: #edf1f5;
    border-radius: 999px;
    overflow: hidden;
    position: relative;
}

.po-rate-fill {
    height: 100%;
    width: <?= $doneRate ?>%;
    background: linear-gradient(90deg, #43a047 0%, #66bb6a 100%);
    border-radius: 999px;
    transition: width 0.4s ease;
}

.po-rate-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    font-size: 12px;
    color: #777;
    gap: 10px;
    flex-wrap: wrap;
}
</style>

<div class="po-rate-card">
    <div class="po-rate-top">
        <div class="po-rate-title">PO Done Rate</div>
        <div class="po-rate-value" id="po-done-rate-text"><?= $doneRate ?>%</div>
    </div>

    <div class="po-rate-meta">
        <span id="po-done-rate-meta"><?= $done ?> of <?= $total ?> purchase orders completed</span>
    </div>

    <div class="po-rate-track">
        <div class="po-rate-fill" id="po-done-rate-fill"></div>
    </div>

    <div class="po-rate-labels">
        <span>0%</span>
        <span>Completion progress</span>
        <span>100%</span>
    </div>
</div>