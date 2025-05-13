<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// FILE: admin/add_semester.php
require_once '../core/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة فصل دراسي</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f2f2f2; padding: 30px; }
    .container { max-width: 800px; margin: auto; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="mb-4 text-success">➕ إضافة فصل دراسي جديد</h3>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $material_id = $_POST['material_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($application_id && $class_id && $material_id && $name && $slug) {
      $check = $conn->prepare("SELECT id FROM semesters WHERE name = ? AND material_id = ? AND application_id = ?");
      $check->bind_param("sii", $name, $material_id, $application_id);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        echo "<div class='alert alert-danger'>⚠️ الفصل موجود مسبقًا لهذه المادة والتطبيق.</div>";
      } else {
        $insert = $conn->prepare("INSERT INTO semesters (application_id, class_id, material_id, name, slug) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iiiss", $application_id, $class_id, $material_id, $name, $slug);
        if ($insert->execute()) {
          echo "<div class='alert alert-success'>✅ تم إضافة الفصل بنجاح.</div>";
        } else {
          echo "<div class='alert alert-danger'>❌ حدث خطأ أثناء الإضافة.</div>";
        }
      }
    } else {
      echo "<div class='alert alert-danger'>⚠️ يرجى تعبئة جميع الحقول.</div>";
    }
  }

  // التطبيقات
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
      <select name="class_id" id="class_id" class="form-select" required onchange="loadMaterials(this.value)">
        <option value="">-- اختر التطبيق أولًا --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اختر المادة</label>
      <select name="material_id" id="material_id" class="form-select" required>
        <option value="">-- اختر الصف أولًا --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اسم الفصل الدراسي</label>
      <input type="text" name="name" id="name" class="form-control" oninput="updateSlug()" required>
    </div>

    <div class="mb-3">
      <label class="form-label">الرابط (slug)</label>
      <input type="text" name="slug" id="slug" class="form-control" readonly required>
    </div>

    <button type="submit" class="btn btn-success w-100">💾 حفظ الفصل</button>
  </form>
</div>

<script>
  function updateSlug() {
    const name = document.getElementById('name').value;
    document.getElementById('slug').value = name.trim().replace(/\s+/g, '-').replace(/[^\w\-ء-ي]+/g, '').toLowerCase();
  }

  function loadClasses(appId) {
    const classSelect = document.getElementById('class_id');
    const materialSelect = document.getElementById('material_id');
    classSelect.innerHTML = '<option>⏳ تحميل الصفوف...</option>';
    materialSelect.innerHTML = '<option>-- اختر الصف أولًا --</option>';

    fetch('/edu.project/admin/ajax/get_classes.php?application_id=' + appId)
      .then(res => res.json())
      .then(data => {
        let options = '<option value="">-- اختر --</option>';
        data.forEach(row => {
          options += `<option value="${row.id}">${row.name}</option>`;
        });
        classSelect.innerHTML = options;
      })
      .catch(err => {
        console.error(err);
        classSelect.innerHTML = '<option>⚠️ خطأ</option>';
      });
  }

  function loadMaterials(classId) {
    const appId = document.getElementById('application_id').value;
    const materialSelect = document.getElementById('material_id');
    materialSelect.innerHTML = '<option>⏳ تحميل المواد...</option>';

    fetch('/edu.project/admin/ajax/get_materials.php?application_id=' + appId + '&class_id=' + classId)
      .then(res => res.json())
      .then(data => {
        let options = '<option value="">-- اختر --</option>';
        data.forEach(row => {
          options += `<option value="${row.id}">${row.name}</option>`;
        });
        materialSelect.innerHTML = options;
      })
      .catch(err => {
        console.error(err);
        materialSelect.innerHTML = '<option>⚠️ خطأ</option>';
      });
  }
</script>
</body>
</html>