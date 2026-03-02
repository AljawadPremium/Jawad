<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo "طلب غير صالح";
    exit;
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
    echo "المتقدم غير موجود";
    exit;
}

include 'header.php';
?>

<section class="career-admin" dir="rtl" style="text-align: right; padding: 40px 20px;">

    <h2 style="color: var(--gold);">تفاصيل المتقدم</h2>

    <div class="admin-card">
        <div
            style="display: flex; justify-content: space-between; align-items: center; background: #fdfbf7; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <div>
                <h3 style="margin: 0;"><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></h3>
                <p style="margin: 5px 0; color: #666;">متقدم لوظيفة:
                    <strong><?= htmlspecialchars($app['title_ar']) ?></strong>
                </p>
            </div>

            <form method="POST"
                style="background: white; padding: 15px; border: 1px solid var(--gold); border-radius: 8px;">
                <label style="font-size: 14px;">تحديث الحالة:</label>
                <select name="status"
                    style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; font-family: inherit;">
                    <option value="pending" <?= $app['status'] == 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                    <option value="review" <?= $app['status'] == 'review' ? 'selected' : '' ?>>مراجعة</option>
                    <option value="interview" <?= $app['status'] == 'interview' ? 'selected' : '' ?>>مقابلة</option>
                    <option value="accepted" <?= $app['status'] == 'accepted' ? 'selected' : '' ?>>مقبول</option>
                    <option value="rejected" <?= $app['status'] == 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                </select>
                <button type="submit" name="update_status"
                    style="background: var(--gold); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">تحديث</button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <p><strong>الجنسية:</strong>
                <?php
                $nat_map = [
                    'Saudi' => 'سعودي',
                    'UAE' => 'إماراتي',
                    'Kuwaiti' => 'كويتي',
                    'Qatari' => 'قطري',
                    'Bahraini' => 'بحريني',
                    'Omani' => 'عماني',
                    'Egyptian' => 'مصري',
                    'Jordanian' => 'أردني',
                    'Syrian' => 'سوري',
                    'Lebanese' => 'لبناني',
                    'Palestinian' => 'فلسطيني',
                    'Yemeni' => 'يمني',
                    'Sudanese' => 'سوداني',
                    'Moroccan' => 'مغربي',
                    'Tunisian' => 'تونسي',
                    'Algerian' => 'جزائري',
                    'Indian' => 'هندي',
                    'Pakistani' => 'باكستاني',
                    'Filipino' => 'فلبيني',
                    'Bangladeshi' => 'بنجلاديشي',
                    'Other' => 'أخرى'
                ];
                echo htmlspecialchars($nat_map[$app['nationality']] ?? $app['nationality']);
                ?>
            </p>
            <p><strong>العمر:</strong>
                <?php
                if (!empty($app['birth_date'])) {
                    $birthDate = new DateTime($app['birth_date']);
                    $today = new DateTime('today');
                    echo $birthDate->diff($today)->y . ' (' . htmlspecialchars($app['birth_date']) . ')';
                } else {
                    echo 'N/A';
                }
                ?>
            </p>
            <p><strong>البريد الإلكتروني:</strong> <?= htmlspecialchars($app['email']) ?></p>
            <p><strong>رقم الجوال:</strong> <?= htmlspecialchars($app['phone']) ?></p>
            <p><strong>المدينة:</strong> <?= htmlspecialchars($app['city']) ?></p>
            <p><strong>الراتب المتوقع:</strong> <?= htmlspecialchars($app['expected_salary']) ?></p>
            <p><strong>آخر راتب:</strong> <?= htmlspecialchars($app['last_salary']) ?></p>
            <p><strong>فترة الإنذار:</strong> <?= htmlspecialchars($app['notice_period']) ?></p>
            <p><strong>المستوى التعليمي:</strong> <?= htmlspecialchars($app['education_level']) ?></p>
            <p><strong>التخصص:</strong> <?= htmlspecialchars($app['major']) ?></p>
            <p><strong>سنوات الخبرة:</strong> <?= htmlspecialchars($app['experience_years']) ?></p>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee;">

        <p><strong>آخر وظيفة سابقة - اسم المنظمة والمسمى
                الوظيفي:</strong><br><?= nl2br(htmlspecialchars($app['experience_details'])) ?></p>
        <p><strong>الدورات التدريبية:</strong><br><?= nl2br(htmlspecialchars($app['courses'])) ?></p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <div style="display: flex; gap: 20px;">
            <a href="<?= htmlspecialchars($app['cv_file']) ?>" target="_blank"
                style="background: var(--dark); color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold;">
                📥 تحميل السيرة الذاتية (CV)
            </a>
            <a href="career-admin.php" style="padding: 12px 25px; text-decoration: none; color: #666;">← العودة
                للقائمة</a>
        </div>
    </div>

</section>

<?php include 'footer.php'; ?>