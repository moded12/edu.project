<?php
// FILE: admin/ajax/get_groups.php
require_once '../../core/db.php';

$section_id = $_GET['section_id'] ?? 0;
$application_id = $_GET['application_id'] ?? 0;
$class_id = $_GET['class_id'] ?? 0;
$material_id = $_GET['material_id'] ?? 0;
$semester_id = $_GET['semester_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name FROM groups 
  WHERE section_id = ? AND application_id = ? AND class_id = ? AND material_id = ? AND semester_id = ?
  ORDER BY id ASC");

$stmt->bind_param("iiiii", $section_id, $application_id, $class_id, $material_id, $semester_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);