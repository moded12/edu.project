<?php
// FILE: /edu.project/public/view_classes.php
require_once('../admin/core/db.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>📚 اختيار الصفوف</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { cairo: ['Cairo', 'sans-serif'] }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">

  <header class="bg-blue-700 text-white py-4 text-center text-2xl font-bold shadow-md mb-6">
    📚 اختر الصف
  </header>

  <div class="container mx-auto max-w-3xl">
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
      <?php
      $res = $conn->query("SELECT * FROM classes ORDER BY id ASC");
      if ($res->num_rows > 0):
        while ($row = $res->fetch_assoc()):
          $class_id = $row['id'];
          $class_name = htmlspecialchars($row['name']);
          echo "
            <div onclick=\"window.location='view_materials.php?class_id=$class_id'\"
                 class='bg-white border border-blue-100 rounded-lg p-3 shadow-sm hover:shadow-md transition text-center cursor-pointer'>
              <h2 class='text-base font-bold text-blue-800'>$class_name</h2>
            </div>";
        endwhile;
      else:
        echo "<div class='col-span-3 text-center text-red-600'>🚫 لا توجد صفوف مضافة.</div>";
      endif;
      ?>
    </div>
  </div>

</body>
</html>
