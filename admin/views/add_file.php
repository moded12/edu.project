<?php
// FILE: admin/views/add_file.php
require_once('../core/db.php');
require_once('../auth.php');

$lessons = $conn->query("SELECT id, name FROM lessons ORDER BY id DESC");

$success = '';
$upload_dir = '../../uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $lesson_id = intval($_POST['lesson_id']);
  $type = $_POST['file_type'];
  $is_protected = isset($_POST['is_protected']) ? 1 : 0;
  $link = '';

  if ($type === 'url') {
    $link = trim($_POST['external_link']);
  } elseif ($_FILES['uploaded_file']['name']) {
    $today = date('Y-m-d');
    $app_folder = 'app_1'; // ثابت حالياً - يمكن تغييره لاحقاً حسب التطبيق
    $path = "$upload_dir$app_folder/$today/";
    if (!is_dir($path)) { mkdir($path, 0777, true); }

    $filename = basename($_FILES['uploaded_file']['name']);
    $target = $path . $filename;
    if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $target)) {
      $link = str_replace('../../', '/edu.project/', $target); // المسار الظاهري
    }
  }

  if ($lesson_id && $type && $link) {
    $stmt = $conn->prepare("INSERT INTO files (lesson_id, file_type, link, is_protected) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $lesson_id, $type, $link, $is_protected);
    $stmt->execute();
    $success = "✅ تم رفع/إضافة الملف بنجاح!";
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة ملف</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f5f5f5; }
    .box { max-width: 700px; margin: 60px auto; background: white; padding: 30px; border-radius: 10px; }
  </style>
</head>
<body>

<div class="box shadow">
  <h4 class="mb-4 text-primary">📎 إضافة أو رفع ملف للدرس</h4>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">اختر الدرس</label>
      <select name="lesson_id" class="form-select" required>
        <option value="">-- اختر --</option>
        <?php while ($row = $lessons->fetch_assoc()): ?>
          <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">نوع الملف</label>
      <select name="file_type" class="form-select" required onchange="toggleFields(this.value)">
        <option value="">-- اختر --</option>
        <option value="pdf">PDF</option>
        <option value="image">صورة</option>
        <option value="video">فيديو</option>
        <option value="url">رابط خارجي</option>
        <option value="word">Word</option>
        <option value="excel">Excel</option>
        <option value="ppt">PowerPoint</option>
        <option value="audio">صوت</option>
        <option value="other">أخرى</option>
      </select>
    </div>

    <div class="mb-3" id="file_input">
      <label class="form-label">رفع الملف</label>
      <input type="file" name="uploaded_file" class="form-control">
    </div>

    <div class="mb-3 d-none" id="link_input">
      <label class="form-label">رابط خارجي (URL)</label>
      <input type="text" name="external_link" class="form-control">
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="is_protected" id="protectedCheck">
      <label class="form-check-label" for="protectedCheck">🔒 حماية الرابط (shneler.com فقط)</label>
    </div>

    <button class="btn btn-success w-100">💾 حفظ الملف</button>
  </form>
</div>

<script>
function toggleFields(type) {
  document.getElementById('file_input').classList.toggle('d-none', type === 'url');
  document.getElementById('link_input').classList.toggle('d-none', type !== 'url');
}
</script>

</body>
</html>
