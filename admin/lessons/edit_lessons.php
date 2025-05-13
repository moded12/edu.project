<?php
require_once '../core/db.php';

// جلب المواضيع من قاعدة البيانات
$sql = "SELECT * FROM lessons ORDER BY class_id ASC";
$result = $conn->query($sql);

// جلب الأقسام لعرضها في قائمة منسدلة
$sections_result = $conn->query("SELECT id, name FROM sections ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update') {
        // التحديث
        $lesson_id = $_POST['lesson_id'];
        $lesson_name = $_POST['lesson_name'];
        $new_class_id = $_POST['class_id'];

        // تحديث الموضوع
        $update_sql = "UPDATE lessons SET name = ?, class_id = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sii", $lesson_name, $new_class_id, $lesson_id);
        $stmt->execute();
        echo "<div class='alert alert-success'>تم التحديث بنجاح</div>";
    } elseif ($_POST['action'] == 'delete') {
        // الحذف
        $lesson_id = $_POST['lesson_id'];

        // حذف الموضوع
        $delete_sql = "DELETE FROM lessons WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $lesson_id);
        $stmt->execute();
        echo "<div class='alert alert-success'>تم حذف الموضوع بنجاح</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الدروس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background: #f9f9f9; padding: 30px; }
        .container { max-width: 900px; margin: auto; }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mb-4 text-success">📘 إدارة الدروس</h3>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>اسم الموضوع</th>
                <th>القسم</th>
                <th>الإجراءات</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td>
                        <?php
                        $class_id = $row['class_id'];
                        $class_result = $conn->query("SELECT name FROM classes WHERE id = $class_id");
                        $class_name = $class_result->fetch_assoc()['name'];
                        echo $class_name;
                        ?>
                    </td>
                    <td>
                        <!-- تعديل -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">تعديل</button>
                        <!-- حذف -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="lesson_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟');">حذف</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal لتعديل الموضوع -->
                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">تعديل الموضوع</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="lesson_id" value="<?php echo $row['id']; ?>">
                                    <div class="mb-3">
                                        <label for="lesson_name" class="form-label">اسم الموضوع</label>
                                        <input type="text" class="form-control" id="lesson_name" name="lesson_name" value="<?php echo $row['name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_id" class="form-label">القسم</label>
                                        <select class="form-select" id="class_id" name="class_id" required>
                                            <option value="">-- اختر --</option>
                                            <?php while ($section = $sections_result->fetch_assoc()): ?>
                                                <option value="<?php echo $section['id']; ?>" <?php if ($section['id'] == $row['class_id']) echo 'selected'; ?>><?php echo $section['name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    <button type="submit" class="btn btn-primary" name="action" value="update">تحديث</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">لا توجد مواضيع لعرضها.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
