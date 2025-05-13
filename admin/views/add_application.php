<?php
// FILE: admin/views/add_application.php
require_once('../core/db.php');
require_once('../auth.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$success = '';
$error = '';

// عملية الإضافة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');

  function generateSlug($text) {
    $text = trim($text);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{Arabic}a-z0-9]+/u', '-', $text);
    $text = trim($text, '-');
    return $text;
  }

  $slug = generateSlug($name);

  if ($name && $slug) {
    $check = $conn->prepare("SELECT COUNT(*) FROM applications WHERE slug = ?");
    $check->bind_param("s", $slug);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
      $error = "❌ اسم التطبيق موجود مسبقًا، يرجى اختيار اسم مختلف.";
    } else {
      $stmt = $conn->prepare("INSERT INTO applications (name, slug, display_order, is_visible) VALUES (?, ?, 0, 1)");
      if ($stmt) {
        $stmt->bind_param("ss", $name, $slug);
        if ($stmt->execute()) {
          $success = "✅ تم إضافة التطبيق بنجاح!";
        } else {
          $error = "❌ فشل التنفيذ: " . $stmt->error;
        }
      } else {
        $error = "❌ فشل التحضير: " . $conn->error;
      }
    }
  } else {
    $error = "❌ الاسم فارغ أو غير صالح.";
  }
}

// جلب التطبيقات الحالية
$apps = [];
$result = $conn->query("SELECT id, name, slug FROM applications ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
  $apps[] = $row;
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
    .box { max-width: 800px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; }
    table { width: 100%; background: #f8f9fa; }
    th, td { padding: 8px; text-align: center; }
    th { background: #e9ecef; }
  </style>
</head>
<body>

<div class="box shadow">
  <h4 class="mb-3 text-primary">📱 التطبيقات المضافة</h4>
  <?php if (count($apps) > 0): ?>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>الاسم</th>
        <th>الرابط (Slug)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($apps as $app): ?>
      <tr>
        <td><?= $app['id'] ?></td>
        <td><?= htmlspecialchars($app['name']) ?></td>
        <td><?= htmlspecialchars($app['slug']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <div class="alert alert-info">لا يوجد تطبيقات بعد.</div>
  <?php endif; ?>
</div>

<div class="box shadow">
  <h4 class="mb-4 text-success">➕ إضافة تطبيق جديد</h4>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

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
