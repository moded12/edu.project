<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// FILE: admin/index.php
require_once('auth.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>لوحة تحكم المعلم الإلكتروني</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Cairo', sans-serif;
      margin: 0;
      overflow: hidden;
    }
    .sidebar {
      background-color: #222;
      height: 100vh;
      color: white;
      padding-top: 1rem;
      position: fixed;
      width: 220px;
    }
    .sidebar a {
      display: block;
      color: white;
      padding: 12px 20px;
      text-decoration: none;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #0d6efd;
    }
    .header {
      background-color: #fff;
      border-bottom: 1px solid #ddd;
      padding: 10px 20px;
      position: fixed;
      right: 220px;
      left: 0;
      top: 0;
      z-index: 1000;
    }
    .iframe-container {
      position: absolute;
      top: 60px;
      right: 220px;
      bottom: 0;
      left: 0;
    }
    iframe {
      width: 100%;
      height: 100%;
      border: none;
    }
  </style>
</head>
<body>


<!-- Sidebar -->
<div class="sidebar">
  <h5 class="text-center mb-4">📘 المعلم الإلكتروني</h5>
  <a href="#" onclick="loadPage('views/add_application.php')">📱 التطبيقات</a>
  <a href="#" onclick="loadPage('views/add_class.php')">📚 الصفوف</a>
  <a href="#" onclick="loadPage('views/add_material.php')">📘 المواد</a>
  <a href="#" onclick="loadPage('views/add_semester.php')">📆 الفصول</a>
  <a href="#" onclick="loadPage('views/add_section.php')">📁 الأقسام</a>
  <a href="#" onclick="loadPage('views/add_group.php')">📂 المجموعات</a>
  <a href="#" onclick="loadPage('views/add_lesson.php')">📖 الدروس</a>
  <a href="#" onclick="loadPage('views/add_file.php')">📎 الملفات</a>
  <a href="#" onclick="loadPage('lessons/edit_lessons.php')">✏️ تعديل وحذف الدروس</a>
<a href="admin/config.php" target="_blank">🔐 تعطيل القراءة الخارجية</a>


  <a href="logout.php">🚪 تسجيل الخروج</a>
</div>




<!-- Header -->
<div class="header">
  <strong>مرحبًا <?= $_SESSION['admin'] ?> - اختر من القائمة</strong>
</div>

<!-- Iframe content -->
<div class="iframe-container">
  <iframe id="mainFrame" src="views/add_application.php"></iframe>
</div>

<script>
function loadPage(page) {
  document.getElementById('mainFrame').src = page;
}
</script>

</body>
</html>