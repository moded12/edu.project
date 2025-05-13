<?php
// FILE: admin/add_class.php
// ROOT: /admin/
require_once '../core/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة صف جديد</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f2f2f2; padding: 30px; }
    .container { max-width: 800px; margin: auto; }
    table { background: white; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="mb-4 text-success">➕ إضافة صف جديد</h3>

  <?php
  // عند الحفظ
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($application_id && $name && $slug) {
      $check = $conn->prepare("SELECT id FROM classes WHERE name = ? AND application_id = ?");
      $check->bind_param("si", $name, $application_id);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        echo "<div class='alert alert-danger'>⚠️ الصف موجود مسبقًا لهذا التطبيق.</div>";
      } else {
        $insert = $conn->prepare("INSERT INTO classes (application_id, name, slug) VALUES (?, ?, ?)");
        $insert->bind_param("iss", $application_id, $name, $slug);
        if ($insert->execute()) {
          echo "<div class='alert alert-success'>✅ تم إضافة الصف بنجاح.</div>";
        } else {
          echo "<div class='alert alert-danger'>❌ حدث خطأ أثناء الإضافة.</div>";
        }
      }
    } else {
      echo "<div class='alert alert-danger'>⚠️ يرجى تعبئة جميع الحقول.</div>";
    }
  }

  // جلب التطبيقات
  $apps = $conn->query("SELECT id, name FROM applications ORDER BY id DESC");
  ?>

  <!-- ✅ نموذج الإضافة -->
  <form method="POST" class="border p-4 bg-white rounded shadow-sm mb-4">
    <div class="mb-3">
      <label class="form-label">اختر التطبيق</label>
      <select name="application_id" class="form-select" required onchange="loadClasses(this.value)">
        <option value="">-- اختر --</option>
        <?php while ($app = $apps->fetch_assoc()): ?>
          <option value="<?= $app['id'] ?>"><?= htmlspecialchars($app['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">اسم الصف</label>
      <input type="text" name="name" id="name" class="form-control" oninput="updateSlug()" required>
    </div>

    <div class="mb-3">
      <label class="form-label">الرابط (slug)</label>
      <input type="text" name="slug" id="slug" class="form-control" readonly required>
    </div>

    <button type="submit" class="btn btn-success w-100">💾 حفظ الصف</button>
  </form>

  <!-- ✅ جدول الصفوف الخاصة بالتطبيق -->
  <table class="table table-bordered bg-white">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>اسم الصف</th>
        <th>الرابط</th>
        <th>التطبيق</th>
      </tr>
    </thead>
    <tbody id="classesTable">
      <tr><td colspan="4">يرجى اختيار تطبيق لعرض صفوفه.</td></tr>
    </tbody>
  </table>
</div>

<!-- ✅ JavaScript في الأسفل -->
<script>
  function generateSlug(str) {
    return str.trim().replace(/\s+/g, '-').replace(/[^\w\-ء-ي]+/g, '').toLowerCase();
  }

  function updateSlug() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    slugInput.value = generateSlug(nameInput.value);
  }

  function loadClasses(appId) {
    const tableBody = document.getElementById('classesTable');
    tableBody.innerHTML = '<tr><td colspan="4">⏳ جاري التحميل...</td></tr>';

    fetch('/edu.project/admin/ajax/get_classes.php?application_id=' + appId)

      .then(response => {
        if (!response.ok) {
          throw new Error('HTTP error: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        console.log('✅ تم تحميل الصفوف:', data); // Debug in console
        let html = '';
        if (data.length > 0) {
          data.forEach(row => {
            html += `<tr>
                      <td>${row.id}</td>
                      <td>${row.name}</td>
                      <td>${row.slug}</td>
                      <td>${row.app_name}</td>
                    </tr>`;
          });
        } else {
          html = '<tr><td colspan="4">لا يوجد صفوف لهذا التطبيق.</td></tr>';
        }
        tableBody.innerHTML = html;
      })
      .catch(error => {
        console.error('❌ خطأ في جلب البيانات:', error);
        tableBody.innerHTML = '<tr><td colspan="4">حدث خطأ أثناء تحميل الصفوف.</td></tr>';
      });
  }
</script>

</body>
</html>