<?php
// FILE: /edu.project/public/view_lessons.php
require_once('../admin/core/db.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>📖 عرض الدروس</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; padding: 30px; background: #f8f8f8; }
    .lesson-box { background: white; border-radius: 12px; box-shadow: 0 0 6px #ccc; padding: 20px; margin-bottom: 20px; }
    .lesson-title { font-weight: bold; color: #0d6efd; }
    .file-link { display: block; margin-top: 10px; color: #198754; text-decoration: none; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-success mb-4">📘 قائمة الدروس</h3>
  <?php
  $sql = "SELECT l.id, l.name AS lesson_name FROM lessons l ORDER BY l.id DESC";
  $result = $conn->query($sql);
  if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
      $lesson_id = $row['id'];
      $lesson_name = htmlspecialchars($row['lesson_name']);
      echo "<div class='lesson-box'>";
      echo "<div class='lesson-title'>📖 $lesson_name</div>";

      $files_res = $conn->query("SELECT path FROM lesson_files WHERE lesson_id = $lesson_id");
      if ($files_res->num_rows > 0) {
        while ($f = $files_res->fetch_assoc()) {
          $path = $f['path'];
          $cleanPath = str_replace('uploads/lessons/', '', $path);
          echo "<a class='file-link' href='../secure_file.php?file=$cleanPath&token=flutter2025_SECRET' target='_blank'>📎 مشاهدة المرفق</a>";
        }
      } else {
        echo "<div class='text-muted'>🚫 لا توجد مرفقات.</div>";
      }

      echo "</div>";
    endwhile;
  else:
    echo "<div class='alert alert-warning'>🚫 لا توجد دروس حاليًا.</div>";
  endif;
  ?>
</div>
</body>
</html>
