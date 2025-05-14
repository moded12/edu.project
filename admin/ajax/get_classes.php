<?php
// FILE: admin/ajax/get_classes.php
require_once '../../core/db.php';

// عرض جميع الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

$app_id = $_GET['application_id'] ?? 0;

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'فشل الاتصال بقاعدة البيانات']);
    exit;
}

// ✅ تحديد الحقول المطلوبة فقط
$stmt = $conn->prepare("SELECT 
                          classes.id, 
                          classes.application_id, 
                          classes.name, 
                          classes.slug, 
                          applications.name AS app_name
                        FROM classes 
                        JOIN applications ON classes.application_id = applications.id 
                        WHERE classes.application_id = ?
                        ORDER BY classes.id DESC");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'فشل تجهيز الاستعلام']);
    exit;
}

$stmt->bind_param("i", $app_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit;