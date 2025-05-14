<?php
// FILE: admin/ajax/get_semesters.php
require_once '../../core/db.php';

$material_id = $_GET['material_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name FROM semesters WHERE material_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);