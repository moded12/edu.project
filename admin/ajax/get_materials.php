<?php
// FILE: admin/ajax/get_materials.php
require_once '../../core/db.php';

$app_id = $_GET['application_id'] ?? 0;
$class_id = $_GET['class_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name FROM materials WHERE application_id = ? AND class_id = ? ORDER BY id ASC");
$stmt->bind_param("ii", $app_id, $class_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);