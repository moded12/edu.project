<?php
// FILE: admin/ajax/get_sections.php
require_once '../../core/db.php';

$semester_id = $_GET['semester_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name FROM sections WHERE semester_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $semester_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);