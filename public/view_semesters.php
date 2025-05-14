<?php
// FILE: /edu.project/public/view_semesters.php
require_once('../admin/core/db.php');

$material_id = intval($_GET['material_id'] ?? 0);
$res = $conn->query("SELECT name FROM materials WHERE id = $material_id");
$material = $res->fetch_assoc();
$material_name = $material ? $material['name'] : 'غير معروف';
?>
<h2 class="hidden"><?= "📘 فصول مادة " . htmlspecialchars($material_name) ?></h2>

<div class="flex flex-col sm:flex-row gap-4 justify-center">
  <div onclick="loadSections(<?= $material_id ?>, 0)"
       class="flex-1 bg-white dark:bg-gray-800 border border-blue-200 rounded-xl p-5 text-center shadow hover:shadow-md cursor-pointer transition">
    <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300">📗 الفصل الأول</h3>
  </div>
  <div onclick="loadSections(<?= $material_id ?>, 1)"
       class="flex-1 bg-white dark:bg-gray-800 border border-green-200 rounded-xl p-5 text-center shadow hover:shadow-md cursor-pointer transition">
    <h3 class="text-lg font-bold text-green-700 dark:text-green-300">📘 الفصل الثاني</h3>
  </div>
</div>
