<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo "طلب غير صالح"; exit;
}

/* HANDLE STATUS UPDATE */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $u_stmt = $conn->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
    $u_stmt->bind_param("si", $new_status, $id);
    $u_stmt->execute();
    $u_stmt->close();
    // Refresh data
}

$stmt = $conn->prepare("
    SELECT a.*, j.title_ar 
    FROM job_applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE a.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if (!$app) {
    echo "المتقدم غير موجود"; exit;
}

include 'header.php';
?>

<section class="career-admin" dir="rtl" style="text-align: right; padding: 40px 20px;">

    <h2 style="color: var(--gold);">تفاصيل المتقدم</h2>

    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; background: #fdfbf7; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <div>
                <h3 style="margin: 0;"><?= htmlspecialchars($app['first_name'].' '.$app['last_name']) ?></h3>
                <p style="margin: 5px 0; color: #666;">متقدم لوظيفة: <strong><?= htmlspecialchars($app['title_ar']) ?></strong></p>
            </div>
            
            <form method="POST" style="background: white; padding: 15px; border: 1px solid var(--gold); border-radius: 8px;">
                <label style="font-size: 14px;">تحديث الحالة:</label>
                <select name="status" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; font-family: inherit;">
                    <option value="قيد الانتظار" <?= $app['status'] == 'قيد الانتظار' ? 'selected' : '' ?>>قيد الانتظار</option>
                    <option value="مراجعة" <?= $app['status'] == 'مراجعة' ? 'selected' : '' ?>>مراجعة</option>
                    <option value="مقابلة" <?= $app['status'] == 'مقابلة' ? 'selected' : '' ?>>مقابلة</option>
                    <option value="قبول" <?= $app['status'] == 'قبول' ? 'selected' : '' ?>>قبول</option>
                    <option value="رفض" <?= $app['status'] == 'رفض' ? 'selected' : '' ?>>رفض</option>
                </select>
                <button type="submit" name="update_status" style="background: var(--gold); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">تحديث</button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <p><strong>البريد الإلكتروني:</strong> <?= htmlspecialchars($app['email']) ?></p>
            <p><strong>رقم الجوال:</strong> <?= htmlspecialchars($app['phone']) ?></p>
            <p><strong>المدينة:</strong> <?= htmlspecialchars($app['city']) ?></p>
            <p><strong>المستوى التعليمي:</strong> <?= htmlspecialchars($app['education_level']) ?></p>
            <p><strong>التخصص:</strong> <?= htmlspecialchars($app['major']) ?></p>
            <p><strong>سنوات الخبرة:</strong> <?= htmlspecialchars($app['experience_years']) ?></p>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee;">

        <p><strong>الدورات التدريبية:</strong><br><?= nl2br(htmlspecialchars($app['courses'])) ?></p>
        <p><strong>تفاصيل الخبرة:</strong><br><?= nl2br(htmlspecialchars($app['experience_details'])) ?></p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <div style="display: flex; gap: 20px;">
            <a href="<?= htmlspecialchars($app['cv_file']) ?>" target="_blank" style="background: var(--dark); color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold;">
                📥 تحميل السيرة الذاتية (CV)
            </a>
            <a href="career-admin.php" style="padding: 12px 25px; text-decoration: none; color: #666;">← العودة للقائمة</a>
        </div>
    </div>

</section>

<?php include 'footer.php'; ?>
