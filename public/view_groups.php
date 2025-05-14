<?php
// FILE: /edu.project/public/view_groups.php
require_once('../admin/core/db.php');

$material_id = intval($_GET['material_id'] ?? 0);
$semester = intval($_GET['semester'] ?? 0);

$semester_name = $semester === 0 ? 'الفصل الأول' : 'الفصل الثاني';

$res = $conn->query("SELECT name FROM materials WHERE id = $material_id");
$material = $res->fetch_assoc();
$material_name = $material ? $material['name'] : 'غير معروف';

// افترض أن الجداول تحتوي على العامود semester في جدول المجموعات
$groups = $conn->query("SELECT id, name FROM groups WHERE material_id = $material_id AND semester = $semester ORDER BY id ASC");
?>
<h2 class="hidden"><?= "📂 مجموعات مادة " . htmlspecialchars($material_name) . " - " . $semester_name ?></h2>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
  <?php while ($row = $groups->fetch_assoc()): ?>
    <div class="bg-white dark:bg-gray-800 border border-blue-100 rounded-lg p-4 shadow hover:shadow-md transition text-center">
      <h3 class="text-base font-semibold text-blue-700 dark:text-blue-300"><?= htmlspecialchars($row['name']) ?></h3>
    </div>
  <?php endwhile; ?>
  <?php if ($groups->num_rows === 0): ?>
    <div class="col-span-2 text-center text-red-500">🚫 لا توجد مجموعات لهذا الفصل.</div>
  <?php endif; ?>
</div>
