<?php
// FILE: /edu.project/secure_file.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/admin/config.php';

function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function getMimeType($ext) {
    $ext = strtolower($ext);
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) return "image/$ext";
    if ($ext === 'pdf') return "application/pdf";
    if ($ext === 'mp4') return "video/mp4";
    return "application/octet-stream";
}

$fileParam = $_GET['file'] ?? '';
$token = $_GET['token'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';

$baseDir = realpath(__DIR__ . '/admin/views/uploads/lessons');
$requestedPath = realpath($baseDir . '/' . $fileParam);

if (!$requestedPath || !startsWith($requestedPath, $baseDir)) {
    http_response_code(404);
    die("❌ File not found.");
}

if ($enableSecureFile) {
    $allowedReferer = 'https://www.shneler.com/edu.project';
    $validReferer = strpos($referer, $allowedReferer) !== false;
    $validToken = $token === 'flutter2025_SECRET';

    if (!$validReferer && !$validToken) {
        http_response_code(403);
        die("🚫 Unauthorized access.");
    }
}

$ext = pathinfo($requestedPath, PATHINFO_EXTENSION);
$contentType = getMimeType($ext);

header("Content-Type: $contentType");
header("Content-Disposition: inline; filename=\"" . basename($requestedPath) . "\"");
readfile($requestedPath);
exit;
?>