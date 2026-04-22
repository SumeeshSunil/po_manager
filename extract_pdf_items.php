<?php
// extract_pdf_items.php
// Called via AJAX when a PDF is uploaded — returns extracted items as JSON

include 'config.php';
checkLogin();

header('Content-Type: application/json');

if (!isset($_FILES['po_pdf']) || $_FILES['po_pdf']['error'] !== 0) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit();
}

$fileExt = strtolower(pathinfo($_FILES['po_pdf']['name'], PATHINFO_EXTENSION));
if ($fileExt !== 'pdf') {
    echo json_encode(['success' => false, 'message' => 'Only PDF files allowed']);
    exit();
}

// Save to a temp location
$tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'po_upload_' . time() . '.pdf';
if (!move_uploaded_file($_FILES['po_pdf']['tmp_name'], $tmpPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save temp file']);
    exit();
}

// Path to the Python script (same folder as this PHP file)
$scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'extract_po_items.py';

// Run Python script and capture output
// Use python3 on Linux/Mac, python on Windows
$python = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'python' : 'python3';
$command = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($tmpPath) . ' --json 2>&1';
$output = shell_exec($command);

// Clean up temp file
@unlink($tmpPath);

if ($output === null) {
    echo json_encode(['success' => false, 'message' => 'Python script failed to run. Make sure Python and pdfplumber are installed.']);
    exit();
}

$data = json_decode(trim($output), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Failed to parse output: ' . htmlspecialchars($output)]);
    exit();
}

echo json_encode([
    'success' => true,
    'header'  => $data['header'] ?? [],
    'items'   => $data['items']  ?? [],
]);