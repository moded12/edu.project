<?php
require_once '../core/db.php';

// التأكد من وجود الملف وتم رفعه بنجاح
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === 0) {
    // معلومات الملف المرفوع
    $fileName = $_FILES['file_upload']['name'];
    $fileTmpPath = $_FILES['file_upload']['tmp_name'];
    $fileSize = $_FILES['file_upload']['size'];
    $fileType = $_FILES['file_upload']['type'];

    // تحديد المجلد الذي سيتم تخزين المرفقات فيه
    $targetDir = "../uploads/lessons/" . date('Y-m-d') . "/";

    // التأكد من وجود المجلد وإنشائه إذا لم يكن موجودًا
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // إنشاء المجلد إذا لم يكن موجودًا
    }

    // تحديد المسار الكامل للملف
    $targetFile = $targetDir . uniqid() . '_' . basename($fileName);

    // محاولة نقل الملف إلى المجلد المحدد
    if (move_uploaded_file($fileTmpPath, $targetFile)) {
        echo "<div class='alert alert-success'>✅ تم رفع الملف بنجاح.</div>";

        // إضافة المرفق إلى قاعدة البيانات (تخزين مسار الملف)
        $lesson_id = 1; // ضع ID الدرس المناسب هنا
        $file_type = 'pdf'; // أو 'image' أو 'file' بناءً على نوع المرفق
        $stmt = $conn->prepare("INSERT INTO lesson_files (lesson_id, type, path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $lesson_id, $file_type, $targetFile);
        $stmt->execute();
        echo "<div class='alert alert-success'>✅ تم حفظ المرفق في قاعدة البيانات.</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ فشل رفع الملف.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>❌ لم يتم تحديد الملف أو حدث خطأ في رفعه.</div>";
}
?>