<?php
// FILE: view_sections.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../admin/core/db.php');

$material_id = intval($_GET['material_id'] ?? 0);
$semester = intval($_GET['semester'] ?? 0);
echo "<pre>DEBUG: material_id = $material_id, semester = $semester</pre>";

$semester_name = $semester == 0 ? 'الفصل الأول' : 'الفصل الثاني';

$material_res = $conn->query("SELECT name FROM materials WHERE id = $material_id");
if (!$material_res) {
  die("<p style='color:red;'>خطأ في جلب اسم المادة: " . $conn->error . "</p>");
}
$material = $material_res->fetch_assoc();
$material_name = $material ? $material['name'] : 'غير معروف';

$sections = $conn->query("SELECT id, name FROM sections WHERE material_id = $material_id AND semester_id = $semester ORDER BY id ASC");
if (!$sections) {
  die("<p style='color:red;'>خطأ في جلب الأقسام: " . $conn->error . "</p>");
}
?>
<h2 class="hidden"><?= "📁 أقسام مادة " . htmlspecialchars($material_name) . " - " . $semester_name ?></h2>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <?php while ($row = $sections->fetch_assoc()): ?>
    <a href="view_groups.php?section_id=<?= $row['id'] ?>"
       class="block bg-white dark:bg-gray-800 border border-blue-100 rounded-xl p-4 text-center shadow hover:shadow-md transition">
      <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300"><?= htmlspecialchars($row['name']) ?></h3>
    </a>
  <?php endwhile; ?>
  <?php if ($sections->num_rows === 0): ?>
    <div class="col-span-2 text-center text-red-500">🚫 لا توجد أقسام لهذا الفصل.</div>
  <?php endif; ?>
</div>