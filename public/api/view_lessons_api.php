<?php
// FILE: /edu.project/public/api/view_lessons_api.php
header('Content-Type: application/json; charset=utf-8');
require_once('../../admin/core/db.php');

$response = [];

$sql = "SELECT l.id, l.name AS lesson_name FROM lessons l ORDER BY l.id DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
  $lesson_id = $row['id'];
  $lesson = [
    "id" => $lesson_id,
    "name" => $row['lesson_name'],
    "attachments" => []
  ];

  $files_res = $conn->query("SELECT path FROM lesson_files WHERE lesson_id = $lesson_id");
  while ($f = $files_res->fetch_assoc()) {
    $cleanPath = str_replace('uploads/lessons/', '', $f['path']);
    $lesson['attachments'][] = "https://www.shneler.com/edu.project/secure_file.php?file=$cleanPath&token=flutter2025_SECRET";
  }

  $response[] = $lesson;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
