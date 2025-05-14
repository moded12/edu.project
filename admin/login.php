<?php
// FILE: admin/login.php
session_start();
require_once('../core/db.php');

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = SHA2(?, 256)");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $_SESSION['admin'] = $username;
    header("Location: index.php");
    exit;
  } else {
    $error = "❌ اسم المستخدم أو كلمة المرور غير صحيحة";
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول - المعلم الإلكتروني</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { background-color: #f9f9f9; font-family: 'Cairo', sans-serif; }
    .login-box { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 10px; }
  </style>
</head>
<body>
<div class="login-box shadow">
  <h3 class="text-center text-primary mb-4">🔐 تسجيل الدخول</h3>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="mb-3">
      <label>اسم المستخدم</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>كلمة المرور</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100">دخول</button>
  </form>
</div>
</body>
</html>
