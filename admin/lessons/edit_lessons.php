<?php
require_once '../../core/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $lesson_id = intval($_POST['lesson_id']);

    // حذف الملفات من السيرفر
    $stmt = $conn->prepare("SELECT path FROM lesson_files WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // المسار الصحيح إلى الملفات داخل admin/views/uploads/...
        $absolutePath = realpath(__DIR__ . '/../views/' . $row['path']);
        if ($absolutePath && file_exists($absolutePath)) {
            unlink($absolutePath);
        }
    }

    // حذف الملفات من قاعدة البيانات
    $conn->query("DELETE FROM lesson_files WHERE lesson_id = $lesson_id");

    // حذف الدرس
    $stmt = $conn->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();

    echo "<div class='alert alert-success'>🗑️ تم حذف الدرس وملفاته بنجاح</div>";
}




// تعديل الدرس
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $lesson_id = intval($_POST['lesson_id']);
    $lesson_name = $_POST['lesson_name'];
    $class_id = intval($_POST['class_id']);

    $stmt = $conn->prepare("UPDATE lessons SET name = ?, class_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $lesson_name, $class_id, $lesson_id);
    $stmt->execute();

    echo "<div class='alert alert-success'>✅ تم تحديث بيانات الدرس</div>";
}

// جلب جميع الدروس
$result = $conn->query("SELECT * FROM lessons ORDER BY class_id ASC");

// جلب جميع الأقسام
$sections_res = $conn->query("SELECT id, name FROM sections ORDER BY name ASC");
$sections = [];
while ($sec = $sections_res->fetch_assoc()) {
    $sections[$sec['id']] = $sec['name'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>✏️ تعديل وحذف الدروس</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f8f8f8; padding: 20px; }
    .container { max-width: 900px; margin: auto; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-success mb-4">📘 إدارة الدروس</h3>

  <?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>📖 اسم الدرس</th>
          <th>📁 القسم</th>
          <th>⚙️ إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $sections[$row['class_id']] ?? 'غير معروف' ?></td>
            <td>
              <!-- زر تعديل -->
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">✏️ تعديل</button>

              <!-- زر حذف -->
              <form method="post" style="display:inline;">
                <input type="hidden" name="lesson_id" value="<?= $row['id'] ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('❗ هل أنت متأكد من حذف هذا الدرس وكل ملفاته؟')">🗑️ حذف</button>
              </form>
            </td>
          </tr>

          <!-- نافذة التعديل -->
          <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="post">
                  <div class="modal-header">
                    <h5 class="modal-title">✏️ تعديل الدرس</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="lesson_id" value="<?= $row['id'] ?>">
                    <div class="mb-3">
                      <label class="form-label">📖 اسم الدرس</label>
                      <input type="text" name="lesson_name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">📁 القسم</label>
                      <select name="class_id" class="form-select" required>
                        <option value="">-- اختر --</option>
                        <?php foreach ($sections as $sec_id => $sec_name): ?>
                          <option value="<?= $sec_id ?>" <?= ($sec_id == $row['class_id']) ? 'selected' : '' ?>><?= $sec_name ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="action" value="update" class="btn btn-primary">💾 حفظ التعديلات</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ إغلاق</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-warning">🚫 لا توجد دروس لعرضها حالياً.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>