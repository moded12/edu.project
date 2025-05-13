<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// FILE: admin/add_material.php
require_once '../core/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة مادة جديدة</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f2f2f2; padding: 30px; }
    .container { max-width: 800px; margin: auto; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="mb-4 text-success">➕ إضافة مادة جديدة</h3>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($application_id && $class_id && $name && $slug) {
      $check = $conn->prepare("SELECT id FROM materials WHERE name = ? AND class_id = ? AND application_id = ?");
      $check->bind_param("sii", $name, $class_id, $application_id);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        echo "<div class='alert alert-danger'>⚠️ المادة موجودة مسبقًا لهذا الصف والتطبيق.</div>";
      } else {
        $insert = $conn->prepare("INSERT INTO materials (application_id, class_id, name, slug) VALUES (?, ?, ?, ?)");
        $insert->bind_param("iiss", $application_id, $class_id, $name, $slug);
        if ($insert->execute()) {
          echo "<div class='alert alert-success'>✅ تم إضافة المادة بنجاح.</div>";
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
      <select name="class_id" id="class_id" class="form-select" required>
        <option value="">-- اختر --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اسم المادة</label>
      <input type="text" name="name" id="name" class="form-control" oninput="updateSlug()" required>
    </div>

    <div class="mb-3">
      <label class="form-label">الرابط (slug)</label>
      <input type="text" name="slug" id="slug" class="form-control" readonly required>
    </div>

    <button type="submit" class="btn btn-success w-100">💾 حفظ المادة</button>
  </form>
</div>

<!-- ✅ JavaScript -->
<script>
  function updateSlug() {
    const name = document.getElementById('name').value;
    document.getElementById('slug').value = name.trim().replace(/\s+/g, '-').replace(/[^\w\-ء-ي]+/g, '').toLowerCase();
  }

  function loadClasses(appId) {
    const classSelect = document.getElementById('class_id');
    classSelect.innerHTML = '<option>⏳ جاري التحميل...</option>';

    fetch('/edu.project/admin/ajax/get_classes.php?application_id=' + appId)
      .then(response => {
        if (!response.ok) throw new Error('HTTP error');
        return response.json();
      })
      .then(data => {
        let options = '<option value="">-- اختر --</option>';
        if (data.length > 0) {
          data.forEach(row => {
            options += `<option value="${row.id}">${row.name}</option>`;
          });
        } else {
          options = '<option value="">لا توجد صفوف</option>';
        }
        classSelect.innerHTML = options;
      })
      .catch(error => {
        console.error('❌ خطأ في تحميل الصفوف:', error);
        classSelect.innerHTML = '<option value="">⚠️ حدث خطأ</option>';
      });
  }
</script>
</body>
</html>