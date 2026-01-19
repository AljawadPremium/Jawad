<?php
include 'db.php';
include 'header.php';

$lang = $_GET['lang'] ?? 'en';
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<p style='margin-top:120px;text-align:center;'>Invalid job.</p>";
    include 'footer.php';
    exit;
}

$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='margin-top:120px;text-align:center;'>Job not found.</p>";
    include 'footer.php';
    exit;
}

$job = $result->fetch_assoc();
?>

<section style="margin-top:120px;" <?= $lang === 'ar' ? 'dir="rtl"' : '' ?>>

    <h2><?= htmlspecialchars($lang === 'ar' ? $job['title_ar'] : $job['title_en']) ?></h2>

    <p>
        <?= nl2br(htmlspecialchars($lang === 'ar' ? $job['description_ar'] : $job['description_en'])) ?>
    </p>

    <hr>

    <ul class="job-details">
        <li><strong><?= $lang === 'ar' ? 'الموقع' : 'Location' ?>:</strong> <?= htmlspecialchars($job['location']) ?></li>
        <li><strong><?= $lang === 'ar' ? 'نوع العمل' : 'Job Type' ?>:</strong> <?= htmlspecialchars($job['job_type']) ?></li>

        <?php if (!empty($job['salary'])): ?>
            <li><strong><?= $lang === 'ar' ? 'الراتب' : 'Salary' ?>:</strong> <?= htmlspecialchars($job['salary']) ?></li>
        <?php endif; ?>

        <li><strong><?= $lang === 'ar' ? 'تاريخ النشر' : 'Published' ?>:</strong> <?= htmlspecialchars($job['publish_date']) ?></li>

        <?php if (!empty($job['end_date'])): ?>
            <li><strong><?= $lang === 'ar' ? 'تاريخ الانتهاء' : 'End Date' ?>:</strong> <?= htmlspecialchars($job['end_date']) ?></li>
        <?php endif; ?>
    </ul>

    <?php if (!empty($job['requirements'])): ?>
        <h4><?= $lang === 'ar' ? 'شروط الوظيفة' : 'Requirements' ?></h4>
        <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($job['tasks'])): ?>
        <h4><?= $lang === 'ar' ? 'مهام الوظيفة' : 'Tasks' ?></h4>
        <p><?= nl2br(htmlspecialchars($job['tasks'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($job['skills'])): ?>
        <h4><?= $lang === 'ar' ? 'المهارات' : 'Skills' ?></h4>
        <p><?= nl2br(htmlspecialchars($job['skills'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($job['qualifications'])): ?>
        <h4><?= $lang === 'ar' ? 'المؤهلات' : 'Qualifications' ?></h4>
        <p><?= nl2br(htmlspecialchars($job['qualifications'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($job['experience'])): ?>
        <h4><?= $lang === 'ar' ? 'الخبرة' : 'Experience' ?></h4>
        <p><?= nl2br(htmlspecialchars($job['experience'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($job['languages'])): ?>
        <h4><?= $lang === 'ar' ? 'اللغات' : 'Languages' ?></h4>
        <p><?= htmlspecialchars($job['languages']) ?></p>
    <?php endif; ?>

    <?php if (!empty($job['gender'])): ?>
        <h4><?= $lang === 'ar' ? 'الجنس' : 'Gender' ?></h4>
        <p><?= htmlspecialchars($job['gender']) ?></p>
    <?php endif; ?>

    <br>

    <!-- Apply Button (UI only for now) -->
    <a href="apply.php?job_id=<?= $job['id'] ?>&lang=<?= $lang ?>" class="careers-btn">
        <?= $lang === 'ar' ? 'التقديم على الوظيفة' : 'Apply Now' ?>
    </a>

    <br><br>

    <a href="<?= $lang === 'ar' ? 'career-ar.php' : 'career.php' ?>" class="apply-btn">
        <?= $lang === 'ar' ? 'العودة' : 'Back to Careers' ?>
    </a>

</section>

<?php include 'footer.php'; ?>
