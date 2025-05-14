<?php
// FILE: /edu.project/public/view_materials.php
require_once('../admin/core/db.php');

$class_id = intval($_GET['class_id'] ?? 0);
$res = $conn->query("SELECT name FROM classes WHERE id = $class_id");
$class = $res->fetch_assoc();
$class_name = $class ? $class['name'] : 'غير معروف';

$materials = $conn->query("SELECT id, name FROM materials WHERE class_id = $class_id ORDER BY id ASC");
?>
<h2 class="hidden"><?= "📚 مواد " . htmlspecialchars($class_name) ?></h2>

<div class="grid gap-4">
  <?php while ($row = $materials->fetch_assoc()): ?>
    <div class="bg-white dark:bg-gray-800 border border-green-100 rounded-lg shadow-sm transition">
      <div onclick="loadSemestersInline(<?= $row['id'] ?>)" class="p-4 cursor-pointer">
        <h2 class="text-base font-bold text-green-800 dark:text-green-300">
          <?= htmlspecialchars($row['name']) ?>
        </h2>
      </div>
      <div id="inline-semesters-<?= $row['id'] ?>" class="px-4 pb-4 hidden"></div>
    </div>
  <?php endwhile; ?>
  <?php if ($materials->num_rows === 0): ?>
    <div class="text-center text-red-600">🚫 لا توجد مواد مضافة لهذا الصف.</div>
  <?php endif; ?>
</div>

<script>
  let currentlyOpen = null;

  function loadSemestersInline(materialId) {
    if (currentlyOpen && currentlyOpen !== materialId) {
      const previous = document.getElementById("inline-semesters-" + currentlyOpen);
      previous.innerHTML = "";
      previous.classList.add("hidden");
    }

    const target = document.getElementById("inline-semesters-" + materialId);
    if (target.classList.contains("hidden")) {
      target.innerHTML = '<div class="text-center text-gray-500 py-2">⏳ تحميل الفصول...</div>';
      target.classList.remove("hidden");
      fetch('view_semesters.php?material_id=' + materialId)
        .then(res => res.text())
        .then(html => {
          target.innerHTML = html;
          currentlyOpen = materialId;
        });
    } else {
      target.innerHTML = "";
      target.classList.add("hidden");
      currentlyOpen = null;
    }
  }
</script>
