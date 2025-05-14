<?php
echo "✅ PHP works<br>";
$file = __DIR__ . "/admin/views/uploads/lessons/2025-05/image/682318de7afb8_22.jpg";
echo "🔍 الملف: $file<br>";

if (file_exists($file)) {
    echo "✅ الملف موجود";
} else {
    echo "❌ الملف غير موجود";
}
?>