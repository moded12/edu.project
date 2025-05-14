<?php
// FILE: /edu.project/public/index.php
require_once('../admin/core/db.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>📘 المعلم الإلكتروني سلطنة عمان</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com">
    function loadSections(materialId, semester) {
      const main = document.getElementById('mainContent');
      const title = document.getElementById('pageTitle');
      main.innerHTML = '<div class="text-center p-6 text-gray-500">⏳ تحميل الأقسام...</div>';

      fetch(`view_sections.php?material_id=${materialId}&semester=${semester}`)
        .then(res => res.text())
        .then(html => {
          const temp = document.createElement('div');
          temp.innerHTML = html;
          const newTitle = temp.querySelector('h2')?.textContent || '📁 الأقسام';
          title.textContent = newTitle;
          main.innerHTML = html;
        });
    }

</script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { cairo: ['Cairo', 'sans-serif'] }
        }
      }
    }
  
    function loadSections(materialId, semester) {
      const main = document.getElementById('mainContent');
      const title = document.getElementById('pageTitle');
      main.innerHTML = '<div class="text-center p-6 text-gray-500">⏳ تحميل الأقسام...</div>';

      fetch(`view_sections.php?material_id=${materialId}&semester=${semester}`)
        .then(res => res.text())
        .then(html => {
          const temp = document.createElement('div');
          temp.innerHTML = html;
          const newTitle = temp.querySelector('h2')?.textContent || '📁 الأقسام';
          title.textContent = newTitle;
          main.innerHTML = html;
        });
    }

</script>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Cairo', sans-serif; transition: background 0.3s, color 0.3s; } </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white min-h-screen">

  <!-- Header -->
  <header class="bg-blue-700 dark:bg-blue-900 text-white py-4 px-4 flex items-center justify-between shadow">
    <a href="https://www.shneler.com/edu.project/public/" title="عودة للرئيسية" class="text-white text-xl hover:text-gray-300 transition">&#8592;</a>
    <a href="https://www.shneler.com/edu.project/public/" class="text-xl font-bold hover:underline text-center flex-1">
      📘 المعلم الإلكتروني سلطنة عمان
    </a>
    <button onclick="toggleDark()" class="text-white text-xl hover:text-yellow-300 transition" title="الوضع الليلي">
      &#9790;
    </button>
  </header>

  <!-- Page title -->
  <div id="pageTitle" class="text-center text-xl font-semibold text-green-700 dark:text-green-300 my-6">
    📚 اختر الصف
  </div>

  <!-- Content area -->
  <div class="container mx-auto max-w-3xl p-4">
    <div id="mainContent">
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <?php
        $res = $conn->query("SELECT * FROM classes ORDER BY id ASC");
        while ($row = $res->fetch_assoc()):
          $class_id = $row['id'];
          $class_name = htmlspecialchars($row['name']);
          echo "
            <div onclick=\"loadMaterials($class_id)\"
                 class='bg-white dark:bg-gray-800 border border-blue-100 rounded-lg p-3 shadow-sm hover:shadow-md transition text-center cursor-pointer'>
              <h2 class='text-base font-bold text-blue-800 dark:text-blue-300'>$class_name</h2>
            </div>";
        endwhile;
        ?>
      </div>
    </div>
  </div>

  <script>
    function toggleDark() {
      document.documentElement.classList.toggle('dark');
    }

    function loadMaterials(classId) {
      const main = document.getElementById('mainContent');
      const title = document.getElementById('pageTitle');
      main.innerHTML = '<div class="text-center p-6 text-gray-500">⏳ تحميل المواد...</div>';

      fetch(`view_materials.php?class_id=${classId}`)
        .then(res => res.text())
        .then(html => {
          const temp = document.createElement('div');
          temp.innerHTML = html;
          const newTitle = temp.querySelector('h2')?.textContent || '📚 المواد';
          title.textContent = newTitle;
          main.innerHTML = temp.innerHTML;
        });
    }

    function loadSemesters(materialId) {
      const main = document.getElementById('mainContent');
      const title = document.getElementById('pageTitle');
      main.innerHTML = '<div class="text-center p-6 text-gray-500">⏳ تحميل الفصول...</div>';

      fetch(`view_semesters.php?material_id=${materialId}`)
        .then(res => res.text())
        .then(html => {
          const temp = document.createElement('div');
          temp.innerHTML = html;
          const newTitle = temp.querySelector('h2')?.textContent || '📘 الفصول';
          title.textContent = newTitle;
          main.innerHTML = html;
        });
    }

    let currentlyOpen = null;

    function loadSemestersInline(materialId) {
      if (currentlyOpen && currentlyOpen !== materialId) {
        const previous = document.getElementById("inline-semesters-" + currentlyOpen);
        if (previous) {
          previous.innerHTML = "";
          previous.classList.add("hidden");
        }
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
  
    function loadSections(materialId, semester) {
      const main = document.getElementById('mainContent');
      const title = document.getElementById('pageTitle');
      main.innerHTML = '<div class="text-center p-6 text-gray-500">⏳ تحميل الأقسام...</div>';

      fetch(`view_sections.php?material_id=${materialId}&semester=${semester}`)
        .then(res => res.text())
        .then(html => {
          const temp = document.createElement('div');
          temp.innerHTML = html;
          const newTitle = temp.querySelector('h2')?.textContent || '📁 الأقسام';
          title.textContent = newTitle;
          main.innerHTML = html;
        });
    }

</script>

</body>
</html>
