<?php
// FILE: /edu.project/public/view_semesters.php (standalone demo version)
require_once('../admin/core/db.php');

$material_id = intval($_GET['material_id'] ?? 0);
$res = $conn->query("SELECT name FROM materials WHERE id = $material_id");
$material = $res->fetch_assoc();
$material_name = $material ? $material['name'] : 'غير معروف';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>📘 فصول مادة <?= htmlspecialchars($material_name) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { cairo: ['Cairo', 'sans-serif'] }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Cairo', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen text-gray-900 p-6">

  <h1 class="text-center text-2xl font-bold text-blue-700 mb-6">📘 فصول مادة <?= htmlspecialchars($material_name) ?></h1>

  <div class="flex flex-col sm:flex-row gap-4 justify-center">
    <div class="flex-1 bg-white border border-blue-200 rounded-xl p-5 text-center shadow hover:shadow-md cursor-pointer transition">
      <h3 class="text-lg font-bold text-blue-700">📗 الفصل الأول</h3>
    </div>
    <div class="flex-1 bg-white border border-green-200 rounded-xl p-5 text-center shadow hover:shadow-md cursor-pointer transition">
      <h3 class="text-lg font-bold text-green-700">📘 الفصل الثاني</h3>
    </div>
  </div>

</body>
</html>
