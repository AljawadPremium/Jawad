<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid ID");
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $location_en = !empty($_POST['location_en']) ? $_POST['location_en'] : $_POST['location'];

    $stmt = $conn->prepare("
        UPDATE jobs SET 
            title_en=?, title_ar=?, description_en=?, description_ar=?, 
            location=?, location_en=?, job_type=?, vacancies=?, salary=?, 
            publish_date=?, end_date=?, requirements=?, requirements_en=?,
            tasks=?, skills=?, qualifications=?, experience=?, 
            work_period=?, languages=?, gender=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sssssssissssssssssssi",
        $_POST['title_en'],
        $_POST['title_ar'],
        $_POST['description_en'],
        $_POST['description_ar'],
        $_POST['location'],
        $location_en,
        $_POST['job_type'],
        $_POST['vacancies'],
        $_POST['salary'],
        $_POST['publish_date'],
        $_POST['end_date'],
        $_POST['requirements'],
        $_POST['requirements_en'],
        $_POST['tasks'],
        $_POST['skills'],
        $_POST['qualifications'],
        $_POST['experience'],
        $_POST['work_period'],
        $_POST['languages'],
        $_POST['gender'],
        $id
    );

    if ($stmt->execute()) {
        header("Location: career-admin.php?updated=1");
        exit;
    } else {
        $error = "Error updating job: " . $conn->error;
    }
}

// Fetch current data
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
include 'header.php';
?>

<section class="career-admin" style="padding: 40px 20px;">
    <h2 style="color: var(--gold); margin-bottom: 30px;">تعديل الوظيفة</h2>

    <?php if (isset($error)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <div
        style="margin-bottom: 20px; display: flex; align-items: center; gap: 20px; background: white; padding: 20px; border-radius: 12px; border: 1px solid #ddd;">
        <?php
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $job_link = $protocol . $host . "/job-details.php?id=" . $id;
        $qr_link = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($job_link);
        ?>
        <img src="<?= $qr_link ?>" alt="Job QR Code"
            style="width: 100px; height: 100px; border: 1px solid #eee; padding: 5px;">
        <div>
            <h4 style="margin: 0 0 5px 0;">QR Code للوظيفة</h4>
            <p style="margin: 0; font-size: 14px; color: #666;">يمكنك نسخ الرابط أو تصوير الـ QR للمشاركة.</p>
            <a href="<?= htmlspecialchars($job_link) ?>" target="_blank"
                style="font-size: 13px; color: var(--gold);"><?= htmlspecialchars($job_link) ?></a>
        </div>
    </div>

    <div class="admin-card">
        <form method="POST" class="admin-form">
            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="field">
                    <label>Job Title (English)</label>
                    <input type="text" name="title_en" value="<?= htmlspecialchars($job['title_en'] ?? '') ?>" required
                        style="width: 100%; padding: 10px;">
                </div>
                <div class="field">
                    <label>عنوان الوظيفة (عربي)</label>
                    <input type="text" name="title_ar" value="<?= htmlspecialchars($job['title_ar'] ?? '') ?>" required
                        style="width: 100%; padding: 10px; direction: rtl;">
                </div>
                <div class="field full" style="grid-column: span 2;">
                    <label>Job Description (English)</label>
                    <textarea name="description_en" required
                        style="width: 100%; height: 100px;"><?= htmlspecialchars($job['description_en'] ?? '') ?></textarea>
                </div>
                <div class="field full" style="grid-column: span 2;">
                    <label>وصف الوظيفة (عربي)</label>
                    <textarea name="description_ar" required
                        style="width: 100%; height: 100px; direction: rtl;"><?= htmlspecialchars($job['description_ar'] ?? '') ?></textarea>
                </div>
                <div class="field">
                    <label>Location (English)</label>
                    <input type="text" name="location_en" value="<?= htmlspecialchars($job['location_en'] ?? '') ?>"
                        required style="width: 100%; padding: 10px;">
                </div>
                <div class="field">
                    <label>الموقع (المدينة - عربي)</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($job['location'] ?? '') ?>" required
                        style="width: 100%; padding: 10px; direction: rtl;">
                </div>
                <div class="field">
                    <label>نوع العمل</label>
                    <select name="job_type" style="width: 100%; padding: 10px;">
                        <?php
                        $types = [
                            'Full-time' => 'دوام كامل (Full-time)',
                            'Part-time' => 'دوام جزئي (Part-time)',
                            'Contract' => 'عقد (Contract)',
                            'Remote' => 'عمل عن بعد (Remote)',
                            'Internship' => 'تدريب (Internship)'
                        ];
                        foreach ($types as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ($job['job_type'] ?? '') == $val ? 'selected' : '' ?>><?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>عدد الشواغر</label>
                    <input type="number" name="vacancies" value="<?= htmlspecialchars($job['vacancies'] ?? '') ?>"
                        style="width: 100%; padding: 10px;">
                </div>
                <div class="field">
                    <label>الراتب</label>
                    <input type="text" name="salary" value="<?= htmlspecialchars($job['salary'] ?? '') ?>"
                        style="width: 100%; padding: 10px;">
                </div>
                <div class="field">
                    <label>تاريخ النشر</label>
                    <input type="date" name="publish_date" value="<?= htmlspecialchars($job['publish_date'] ?? '') ?>"
                        style="width: 100%; padding: 10px;">
                </div>
                <div class="field">
                    <label>تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($job['end_date'] ?? '') ?>"
                        style="width: 100%; padding: 10px;">
                </div>
                <div class="field full" style="grid-column: span 2;">
                    <label>Requirements (English)</label>
                    <textarea name="requirements_en"
                        style="width: 100%; height: 80px;"><?= htmlspecialchars($job['requirements_en'] ?? '') ?></textarea>
                </div>
                <div class="field full" style="grid-column: span 2;">
                    <label>المتطلبات (عربي)</label>
                    <textarea name="requirements"
                        style="width: 100%; height: 80px; direction: rtl;"><?= htmlspecialchars($job['requirements'] ?? '') ?></textarea>
                </div>
                <!-- Hidden fields to maintain existing structure if needed -->
                <input type="hidden" name="tasks" value="<?= htmlspecialchars($job['tasks'] ?? '') ?>">
                <input type="hidden" name="skills" value="<?= htmlspecialchars($job['skills'] ?? '') ?>">
                <input type="hidden" name="qualifications"
                    value="<?= htmlspecialchars($job['qualifications'] ?? '') ?>">
                <input type="hidden" name="experience" value="<?= htmlspecialchars($job['experience'] ?? '') ?>">
                <input type="hidden" name="work_period" value="<?= htmlspecialchars($job['work_period'] ?? '') ?>">
                <input type="hidden" name="languages" value="<?= htmlspecialchars($job['languages'] ?? '') ?>">
                <input type="hidden" name="gender" value="<?= htmlspecialchars($job['gender'] ?? '') ?>">
            </div>
            <div style="margin-top: 20px;">
                <button type="submit" class="admin-btn"
                    style="background: var(--gold); color: white; padding: 12px 30px; border: none; cursor: pointer; border-radius: 5px;">تحديث
                    الوظيفة</button>
                <a href="career-admin.php" style="margin-left: 15px; color: #666; text-decoration: none;">إلغاء</a>
            </div>
        </form>
    </div>
</section>

<?php include 'footer.php'; ?>