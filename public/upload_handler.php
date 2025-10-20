<?php
ob_start(); // prevent premature output

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache');
header('Content-Type: application/json');

// -----------------------------------------------------------------------------
// Configuration
// -----------------------------------------------------------------------------
$uploadDir = __DIR__ . '/qa-uploads/';
$logFile   = $uploadDir . 'debug.log';

// -----------------------------------------------------------------------------
// Logging helper
// -----------------------------------------------------------------------------
function debug_log($message) {
    global $logFile;
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }

    // Rotate if file > 1MB
    if (file_exists($logFile) && filesize($logFile) > 1024 * 1024) {
        rename($logFile, $logFile . '.' . date('Ymd_His'));
    }

    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// -----------------------------------------------------------------------------
// Ensure upload directory exists
// -----------------------------------------------------------------------------
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// -----------------------------------------------------------------------------
// Upload handling
// -----------------------------------------------------------------------------
try {
    if (!empty($_FILES['upload']['tmp_name'])) {
        $tmpName  = $_FILES['upload']['tmp_name'];
        $fileName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['upload']['name']);
        $targetFile = $uploadDir . $fileName;

        debug_log("Incoming file upload: tmp=$tmpName, target=$targetFile");

        if (!move_uploaded_file($tmpName, $targetFile)) {
            debug_log("ERROR: move_uploaded_file failed for $tmpName → $targetFile");
            throw new Exception('Failed to move uploaded file.');
        }

        $url = '/qa-uploads/' . $fileName;
        debug_log("SUCCESS: Saved file to $url");

        ob_end_clean();
        echo json_encode(['uploaded' => 1, 'fileName' => $fileName, 'url' => $url]);
        exit;
    }

    // Handle pasted base64 image
    $raw = file_get_contents('php://input');
    if (preg_match('/data:image\/(\w+);base64,/', $raw, $type)) {
        $ext = strtolower($type[1]);
        $fileName = uniqid('img_', true) . ".$ext";
        $filePath = $uploadDir . $fileName;

        $data = base64_decode(substr($raw, strpos($raw, ',') + 1));
        if ($data === false) {
            debug_log("ERROR: Base64 decode failed");
            throw new Exception('Base64 decode failed.');
        }

        file_put_contents($filePath, $data);
        debug_log("SUCCESS: Pasted image saved to $filePath");

        $url = '/qa-uploads/' . $fileName;
        ob_end_clean();
        echo json_encode(['uploaded' => 1, 'fileName' => $fileName, 'url' => $url]);
        exit;
    }

    debug_log("ERROR: No upload or base64 data received");
    throw new Exception('No upload or image data received.');

} catch (Throwable $e) {
    debug_log("EXCEPTION: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['uploaded' => 0, 'error' => ['message' => $e->getMessage()]]);
}

