<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// FILE: admin/add_group.php
require_once '../core/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة مجموعة</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f2f2f2; padding: 30px; }
    .container { max-width: 800px; margin: auto; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="mb-4 text-success">➕ إضافة مجموعة جديدة</h3>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $material_id = $_POST['material_id'] ?? '';
    $semester_id = $_POST['semester_id'] ?? '';
    $section_id = $_POST['section_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($application_id && $class_id && $material_id && $semester_id && $section_id && $name && $slug) {
      $check = $conn->prepare("SELECT id FROM groups WHERE name = ? AND section_id = ? AND application_id = ?");
      $check->bind_param("sii", $name, $section_id, $application_id);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        echo "<div class='alert alert-danger'>⚠️ المجموعة موجودة مسبقًا لهذا القسم.</div>";
      } else {
        $insert = $conn->prepare("INSERT INTO groups (application_id, class_id, material_id, semester_id, section_id, name, slug) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("iiiiiss", $application_id, $class_id, $material_id, $semester_id, $section_id, $name, $slug);
        if ($insert->execute()) {
          echo "<div class='alert alert-success'>✅ تم إضافة المجموعة بنجاح.</div>";
        } else {
          echo "<div class='alert alert-danger'>❌ حدث خطأ أثناء الإضافة.</div>";
        }
      }
    } else {
      echo "<div class='alert alert-danger'>⚠️ يرجى تعبئة جميع الحقول.</div>";
    }
  }

  $apps = $conn->query("SELECT id, name FROM applications ORDER BY id DESC");
  ?>

  <form method="POST" class="border p-4 bg-white rounded shadow-sm mb-4">
    <div class="mb-3">
      <label class="form-label">اختر التطبيق</label>
      <select name="application_id" id="application_id" class="form-select" required onchange="loadClasses(this.value)">
        <option value="">-- اختر --</option>
        <?php while ($app = $apps->fetch_assoc()): ?>
          <option value="<?= $app['id'] ?>"><?= htmlspecialchars($app['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اختر الصف</label>
      <select name="class_id" id="class_id" class="form-select" required onchange="loadMaterials()">
        <option value="">-- اختر التطبيق أولًا --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اختر المادة</label>
      <select name="material_id" id="material_id" class="form-select" required onchange="loadSemesters()">
        <option value="">-- اختر الصف أولًا --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اختر الفصل الدراسي</label>
      <select name="semester_id" id="semester_id" class="form-select" required onchange="loadSections()">
        <option value="">-- اختر المادة أولًا --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اختر القسم</label>
      <select name="section_id" id="section_id" class="form-select" required>
        <option value="">-- اختر الفصل أولًا --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اسم المجموعة</label>
      <input type="text" name="name" id="name" class="form-control" oninput="updateSlug()" required>
    </div>

    <div class="mb-3">
      <label class="form-label">الرابط (slug)</label>
      <input type="text" name="slug" id="slug" class="form-control" readonly required>
    </div>

    <button type="submit" class="btn btn-success w-100">💾 حفظ المجموعة</button>
  </form>
</div>

<script>
  function updateSlug() {
    const name = document.getElementById('name').value;
    document.getElementById('slug').value = name.trim().replace(/\s+/g, '-').replace(/[^\w\-ء-ي]+/g, '').toLowerCase();
  }

  function loadClasses(appId) {
    fetch('/edu.project/admin/ajax/get_classes.php?application_id=' + appId)
      .then(res => res.json())
      .then(data => {
        const classSelect = document.getElementById('class_id');
        let options = '<option value="">-- اختر --</option>';
        data.forEach(row => {
          options += `<option value="${row.id}">${row.name}</option>`;
        });
        classSelect.innerHTML = options;
        document.getElementById('material_id').innerHTML = '<option>-- اختر الصف أولًا --</option>';
        document.getElementById('semester_id').innerHTML = '<option>-- اختر المادة --</option>';
        document.getElementById('section_id').innerHTML = '<option>-- اختر الفصل --</option>';
      });
  }

  function loadMaterials() {
    const appId = document.getElementById('application_id').value;
    const classId = document.getElementById('class_id').value;
    fetch('/edu.project/admin/ajax/get_materials.php?application_id=' + appId + '&class_id=' + classId)
      .then(res => res.json())
      .then(data => {
        const materialSelect = document.getElementById('material_id');
        let options = '<option value="">-- اختر --</option>';
        data.forEach(row => {
          options += `<option value="${row.id}">${row.name}</option>`;
        });
        materialSelect.innerHTML = options;
        document.getElementById('semester_id').innerHTML = '<option>-- اختر المادة --</option>';
        document.getElementById('section_id').innerHTML = '<option>-- اختر الفصل --</option>';
      });
  }

  function loadSemesters() {
    const materialId = document.getElementById('material_id').value;
    fetch('/edu.project/admin/ajax/get_semesters.php?material_id=' + materialId)
      .then(res => res.json())
      .then(data => {
        const semesterSelect = document.getElementById('semester_id');
        let options = '<option value="">-- اختر --</option>';
        data.forEach(row => {
          options += `<option value="${row.id}">${row.name}</option>`;
        });
        semesterSelect.innerHTML = options;
        document.getElementById('section_id').innerHTML = '<option>-- اختر الفصل --</option>';
      });
  }

  function loadSections() {
    const semesterId = document.getElementById('semester_id').value;
    fetch('/edu.project/admin/ajax/get_sections.php?semester_id=' + semesterId)
      .then(res => res.json())
      .then(data => {
        const sectionSelect = document.getElementById('section_id');
        let options = '<option value="">-- اختر --</option>';
        data.forEach(row => {
          options += `<option value="${row.id}">${row.name}</option>`;
        });
        sectionSelect.innerHTML = options;
      });
  }
</script>
</body>
</html>