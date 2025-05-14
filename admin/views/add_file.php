<?php
// FILE: views/add_file.php
require_once('../core/db.php');
date_default_timezone_set('Asia/Amman');

// دالة لتحديد نوع الملف
function getFileType($extension) {
    $ext = strtolower($extension);
    return match(true) {
        in_array($ext, ['jpg','jpeg','png','gif','bmp','svg']) => 'image',
        in_array($ext, ['pdf']) => 'pdf',
        in_array($ext, ['mp4','mov','webm']) => 'video',
        in_array($ext, ['doc','docx']) => 'word',
        in_array($ext, ['ppt','pptx']) => 'ppt',
        in_array($ext, ['xls','xlsx']) => 'excel',
        in_array($ext, ['mp3','wav','ogg']) => 'audio',
        default => 'other'
    };
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $lesson_id = intval($_POST['lesson_id']);
    $monthFolder = date('Y-m'); // مجلد شهري
    $fileName = basename($_FILES["file"]["name"]);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileType = getFileType($fileExtension);
    $uniqueFile = uniqid() . '_' . $fileName;

    // المسار الكامل
    $baseUploadsDir = realpath(__DIR__ . '/uploads/lessons');
    $uploadDir = $baseUploadsDir . "/$monthFolder/$fileType/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetPath = $uploadDir . $uniqueFile;
    $relativePath = "uploads/lessons/$monthFolder/$fileType/" . $uniqueFile;

    echo "<pre>📁 مسار الحفظ: $relativePath</pre>"; // Debug للمسار

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetPath)) {
        $stmt = $conn->prepare("INSERT INTO lesson_files (lesson_id, type, path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $lesson_id, $fileType, $relativePath);
        $stmt->execute();
        echo "<div class='alert alert-success'>✅ تم رفع الملف بنجاح</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ فشل في رفع الملف.</div>";
    }
}
?>

<!-- 🧾 نموذج رفع -->
<div class="container mt-4">
  <h4>📎 رفع ملف لدرس محدد</h4>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label>📖 رقم الدرس (lesson_id)</label>
      <input type="number" name="lesson_id" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>📂 اختر ملفًا</label>
      <input type="file" name="file" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">🔼 رفع</button>
  </form>
</div>
