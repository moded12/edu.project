<?php
// FILE: admin/views/add_application.php
require_once('../core/db.php');
require_once('auth.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$success = '';
$debug_log = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $debug_log .= "<div class='text-danger'>✅ تم إرسال النموذج</div>";

  $name = trim($_POST['name'] ?? '');
  $debug_log .= "<div>الاسم: $name</div>";

  function generateSlug($text) {
    $text = trim($text);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{Arabic}a-z0-9]+/u', '-', $text);
    $text = trim($text, '-');
    return $text;
  }

  $slug = generateSlug($name);
  $debug_log .= "<div>Slug الناتج: $slug</div>";

  if ($name && $slug) {
    $stmt = $conn->prepare("INSERT INTO applications (name, slug, display_order, is_visible) VALUES (?, ?, 0, 1)");
    if ($stmt) {
      $stmt->bind_param("ss", $name, $slug);
      if ($stmt->execute()) {
        $success = "✅ تم إضافة التطبيق بنجاح!";
      } else {
        $debug_log .= "<div class='text-danger'>❌ فشل التنفيذ: " . $stmt->error . "</div>";
      }
    } else {
      $debug_log .= "<div class='text-danger'>❌ فشل التحضير: " . $conn->error . "</div>";
    }
  } else {
    $debug_log .= "<div class='text-danger'>❌ الاسم أو slug فارغ</div>";
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة تطبيق</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f9f9f9; }
    .box { max-width: 700px; margin: 60px auto; background: white; padding: 30px; border-radius: 10px; }
  </style>
</head>
<body>

<div class="box shadow">
  <h4 class="mb-4 text-primary">📱 إضافة تطبيق جديد</h4>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <?= $debug_log ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">اسم التطبيق</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <button class="btn btn-success w-100">💾 حفظ التطبيق</button>
  </form>
</div>

</body>
</html>
