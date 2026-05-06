<?php
/**
 * save_user_company.php
 * ─────────────────────
 * Saves the non-admin user's chosen company to the PHP session AND the
 * `users` table so it survives localStorage being cleared.
 *
 * SETUP (run once):
 *   ALTER TABLE users ADD COLUMN company VARCHAR(100) DEFAULT NULL;
 *
 * Then in your partials/header.php (after session_start / DB connect),
 * add this block to auto-restore the session value on login:
 *
 *   if (!isset($_SESSION['user_company']) && isset($_SESSION['user_id'])) {
 *       $stmt = $conn->prepare("SELECT company FROM users WHERE id = ?");
 *       $stmt->bind_param("i", $_SESSION['user_id']);
 *       $stmt->execute();
 *       $r = $stmt->get_result()->fetch_assoc();
 *       if (!empty($r['company'])) $_SESSION['user_company'] = $r['company'];
 *   }
 */

include 'partials/header.php';   // starts session, sets $conn, calls checkLogin()

header('Content-Type: application/json');

/* Only accept AJAX POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

/* Must be a logged-in non-admin user */
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

/* Admins don't need a company lock — reject silently */
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    echo json_encode(['success' => true, 'note' => 'Admin — no lock needed']);
    exit;
}

$company = trim($_POST['company'] ?? '');
if ($company === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Company cannot be empty']);
    exit;
}

/* Sanitise: allow only values that actually exist in purchase_orders.platform */
$checkStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM purchase_orders WHERE TRIM(platform) = ?");
$checkStmt->bind_param("s", $company);
$checkStmt->execute();
$checkRow = $checkStmt->get_result()->fetch_assoc();
if ((int)($checkRow['cnt'] ?? 0) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid company value']);
    exit;
}

/* 1 — Save to session (instant effect this request) */
$_SESSION['user_company'] = $company;

/* 2 — Save to DB (permanent across all browsers / devices for this user) */
$userId = (int)$_SESSION['user_id'];
$stmt   = $conn->prepare("UPDATE users SET company = ? WHERE id = ?");
$stmt->bind_param("si", $company, $userId);
$ok = $stmt->execute();

echo json_encode([
    'success' => $ok,
    'company' => $company,
]);