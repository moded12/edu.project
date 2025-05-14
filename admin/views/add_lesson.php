<?php
require_once '../core/db.php';
ini_set('log_errors', 1);
ini_set('error_log', '/httpdocs/edu.project/logs/error_log');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$lastLessonId = null;

// جلب التطبيقات من قاعدة البيانات
$applications = $conn->query("SELECT id, name FROM applications ORDER BY id DESC");

$lastSelections = [
  'application_id' => $_POST['application_id'] ?? '',
  'class_id' => $_POST['class_id'] ?? '',
  'material_id' => $_POST['material_id'] ?? '',
  'semester_id' => $_POST['semester_id'] ?? '',
  'section_id' => $_POST['section_id'] ?? '',
  'group_id' => $_POST['group_id'] ?? ''
];

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $slug = trim($_POST['slug'] ?? '');
  extract($lastSelections);
  $file_type = $_POST['file_type'] ?? '';
  $link_url = $_POST['link_url'] ?? '';
  $slider_urls = $_POST['slider_urls'] ?? [];  // الحصول على الروابط من النموذج

  if ($application_id && $class_id && $material_id && $semester_id && $section_id && $group_id && $name && $slug) {
    $check = $conn->prepare("SELECT id FROM lessons WHERE name = ? AND group_id = ? AND application_id = ?");
    $check->bind_param("sii", $name, $group_id, $application_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      echo "<div class='alert alert-danger'>⚠️ هذا الدرس موجود مسبقًا في هذه المجموعة.</div>";
    } else {
      $insert = $conn->prepare("INSERT INTO lessons (application_id, class_id, material_id, semester_id, section_id, group_id, name, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $insert->bind_param("iiiiisss", $application_id, $class_id, $material_id, $semester_id, $section_id, $group_id, $name, $slug);
      if ($insert->execute()) {
        $lastLessonId = $insert->insert_id;
        echo "<div class='alert alert-success'>✅ تم حفظ الدرس بنجاح.</div>";

        // حفظ الروابط (URL Slider)
        foreach ($slider_urls as $url) {
          $stmt = $conn->prepare("INSERT INTO lesson_urls (lesson_id, url) VALUES (?, ?)");
          $stmt->bind_param("is", $lastLessonId, $url);
          $stmt->execute();
        }

// حفظ المرفق إذا تم اختياره
if ($file_type === 'link' && $link_url) {
  // حفظ الرابط في جدول lesson_urls وليس في lesson_files
  $stmt = $conn->prepare("INSERT INTO lesson_urls (lesson_id, url) VALUES (?, ?)");
  $stmt->bind_param("is", $lastLessonId, $link_url);
  $stmt->execute();
} elseif (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
  $originalName = $_FILES['file_upload']['name'];
  $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);

  function getFileType($ext) {
    $ext = strtolower($ext);
    return match (true) {
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

  $fileType = getFileType($fileExtension);
  $monthFolder = date('Y-m');
  $uniqueFileName = uniqid() . '_' . $originalName;

  $uploadDir = __DIR__ . "/uploads/lessons/$monthFolder/$fileType/";
  $relativePath = "uploads/lessons/$monthFolder/$fileType/";
  $targetPath = $uploadDir . $uniqueFileName;
  $relativeFile = $relativePath . $uniqueFileName;

  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetPath)) {
    $stmt = $conn->prepare("INSERT INTO lesson_files (lesson_id, type, path) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $lastLessonId, $fileType, $relativeFile);
    $stmt->execute();
    echo "<div class='alert alert-success'>✅ تم رفع الملف كمرفق.</div>";
  } else {
    echo "<div class='alert alert-danger'>❌ فشل رفع الملف.</div>";
  }
}


        // تفريغ فقط الاسم والرابط لإدخال جديد
        $name = $slug = '';
      } else {
        echo "<div class='alert alert-danger'>❌ فشل في حفظ الدرس.</div>";
      }
    }
  } else {
    echo "<div class='alert alert-danger'>⚠️ يرجى تعبئة جميع الحقول المطلوبة.</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة درس جديد مع مرفق</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f9f9f9; padding: 30px; }
    .container { max-width: 900px; margin: auto; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="mb-4 text-success">📘 إضافة درس جديد مع مرفق</h3>

  <form method="POST" enctype="multipart/form-data" class="bg-white border p-4 rounded shadow-sm mb-4">
    <div class="mb-3">
      <label class="form-label">اسم الدرس</label>
      <input type="text" name="name" id="name" value="<?= htmlspecialchars($name ?? '') ?>" class="form-control" oninput="updateSlug()" required>
    </div>

    <div class="mb-3">
      <label class="form-label">الرابط (slug)</label>
      <input type="text" name="slug" id="slug" value="<?= htmlspecialchars($slug ?? '') ?>" class="form-control" readonly required>
    </div>

    <div class="row g-2">
      <div class="col">
        <label>اختر التطبيق</label>
        <select name="application_id" id="application_id" class="form-select" required>
          <option value="">-- اختر --</option>
          <?php while($app = $applications->fetch_assoc()): ?>
            <option value="<?= $app['id'] ?>" <?= ($lastSelections['application_id'] == $app['id']) ? 'selected' : '' ?>><?= htmlspecialchars($app['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col">
        <label>الصف</label>
        <select name="class_id" id="class_id" class="form-select" required></select>
      </div>
      <div class="col">
        <label>المادة</label>
        <select name="material_id" id="material_id" class="form-select" required></select>
      </div>
    </div>

    <div class="row g-2 mt-2">
      <div class="col">
        <label>الفصل الدراسي</label>
        <select name="semester_id" id="semester_id" class="form-select" required></select>
      </div>
      <div class="col">
        <label>القسم</label>
        <select name="section_id" id="section_id" class="form-select" required></select>
      </div>
      <div class="col">
        <label>المجموعة</label>
        <select name="group_id" id="group_id" class="form-select" required></select>
      </div>
    </div>

    <hr class="my-4">
    <h5 class="text-primary">📎 مرفق الدرس</h5>
    <div class="mb-3">
      <label class="form-label">نوع المرفق</label>
      <select name="file_type" class="form-select" required>
        <option value="pdf">📄 ملف PDF</option>
        <option value="image">🖼️ صورة</option>
        <option value="video">🎥 فيديو</option>
        <option value="file">📁 ملف عام</option>
        <option value="link">🔗 رابط خارجي</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">رابط خارجي (إن وُجد)</label>
      <input type="url" name="link_url" class="form-control" placeholder="https://example.com">
    </div>
    <div class="mb-3">
      <label class="form-label">أو اختر ملف</label>
      <input type="file" name="file_upload" class="form-control">
    </div>
    <button type="submit" name="save_lesson" class="btn btn-success w-100">💾 حفظ الدرس والمرفق</button>
  </form>
</div>

<script>
function updateSlug() {
  const name = document.getElementById('name').value;
  document.getElementById('slug').value = name.trim().replace(/\s+/g, '-').replace(/[^\w\-ء-ي]+/g, '').toLowerCase();
}

// تحميل ديناميكي للمنسدلات
const api = '/edu.project/admin/ajax';
const selectors = ['application_id', 'class_id', 'material_id', 'semester_id', 'section_id', 'group_id'];

function fetchOptions(target, url) {
  fetch(url).then(res => res.json()).then(data => {
    let html = '<option value="">-- اختر --</option>';
    data.forEach(row => html += `<option value="${row.id}">${row.name}</option>`);
    document.getElementById(target).innerHTML = html;
  });
}

document.getElementById('application_id').onchange = e => {
  fetchOptions('class_id', `${api}/get_classes.php?application_id=${e.target.value}`);
  document.getElementById('material_id').innerHTML = '';
};

document.getElementById('class_id').onchange = e => {
  const appId = document.getElementById('application_id').value;
  fetchOptions('material_id', `${api}/get_materials.php?application_id=${appId}&class_id=${e.target.value}`);
};

document.getElementById('material_id').onchange = e => {
  fetchOptions('semester_id', `${api}/get_semesters.php?material_id=${e.target.value}`);
};

document.getElementById('semester_id').onchange = e => {
  fetchOptions('section_id', `${api}/get_sections.php?semester_id=${e.target.value}`);
};

document.getElementById('section_id').onchange = e => {
  const params = new URLSearchParams({
    application_id: document.getElementById('application_id').value,
    class_id: document.getElementById('class_id').value,
    material_id: document.getElementById('material_id').value,
    semester_id: document.getElementById('semester_id').value,
    section_id: e.target.value
  });
  fetchOptions('group_id', `${api}/get_groups.php?${params.toString()}`);
};
</script>
</body>
</html>