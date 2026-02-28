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

// Language fallbacks for display
$display_title = ($lang === 'ar') 
    ? (!empty($job['title_ar']) ? $job['title_ar'] : $job['title_en'])
    : (!empty($job['title_en']) ? $job['title_en'] : $job['title_ar']);

$display_description = ($lang === 'ar')
    ? (!empty($job['description_ar']) ? $job['description_ar'] : $job['description_en'])
    : (!empty($job['description_en']) ? $job['description_en'] : $job['description_ar']);

// Helper function to render multi-line text into a bulleted list
function renderBulletedList($text) {
    if (empty(trim($text ?? ''))) return;
    $lines = explode("\n", trim($text ?? ''));
    echo "<ul>";
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            echo "<li>" . htmlspecialchars($line) . "</li>";
        }
    }
    echo "</ul>";
}
?>

<div class="job-details-wrapper" <?= $lang === 'ar' ? 'dir="rtl"' : '' ?>>
    <div class="job-details-box">
        
        <div class="job-header">
            <h1 class="job-title"><?= htmlspecialchars($display_title) ?></h1>
            <p class="job-summary">
                <?= nl2br(htmlspecialchars($display_description)) ?>
            </p>
        </div>

        <div class="job-meta-grid">
            <div>
                <span><?= $lang === 'ar' ? 'الموقع' : 'Location' ?></span>
                <?php 
                $display_location = ($lang === 'ar')
                    ? (!empty($job['location']) ? $job['location'] : ($job['location_en'] ?? ''))
                    : (!empty($job['location_en']) ? $job['location_en'] : ($job['location'] ?? ''));
                echo htmlspecialchars($display_location);
                ?>
            </div>
            <div>
                <span><?= $lang === 'ar' ? 'نوع العمل' : 'Job Type' ?></span>
                <?php 
                $type_map = [
                    'Full-time' => 'دوام كامل',
                    'Part-time' => 'دوام جزئي',
                    'Contract' => 'عقد',
                    'Remote' => 'عمل عن بعد',
                    'Internship' => 'تدريب'
                ];
                echo htmlspecialchars($lang === 'ar' ? ($type_map[$job['job_type']] ?? $job['job_type']) : $job['job_type']);
                ?>
            </div>
            <?php if (!empty($job['salary'])): ?>
            <div>
                <span><?= $lang === 'ar' ? 'الراتب' : 'Salary' ?></span>
                <?= htmlspecialchars($job['salary']) ?>
            </div>
            <?php endif; ?>
            <div>
                <span><?= $lang === 'ar' ? 'تاريخ النشر' : 'Published' ?></span>
                <?= htmlspecialchars($job['publish_date']) ?>
            </div>
            <?php if (!empty($job['end_date'])): ?>
            <div>
                <span><?= $lang === 'ar' ? 'تاريخ الانتهاء' : 'End Date' ?></span>
                <?= htmlspecialchars($job['end_date']) ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- 2-Column Grid for Details -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 40px;">
            <?php 
            $req = ($lang === 'ar') 
                ? (!empty($job['requirements']) ? $job['requirements'] : $job['requirements_en'])
                : (!empty($job['requirements_en']) ? $job['requirements_en'] : $job['requirements']);
            
            if (!empty(trim($req ?? ''))): ?>
                <div class="job-section" style="margin-bottom: 0;">
                    <h3><?= $lang === 'ar' ? 'شروط الوظيفة' : 'Requirements' ?></h3>
                    <?php renderBulletedList($req); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty(trim($job['tasks'] ?? ''))): ?>
                <div class="job-section" style="margin-bottom: 0;">
                    <h3><?= $lang === 'ar' ? 'مهام الوظيفة' : 'Tasks' ?></h3>
                    <?php renderBulletedList($job['tasks']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty(trim($job['skills'] ?? ''))): ?>
                <div class="job-section" style="margin-bottom: 0;">
                    <h3><?= $lang === 'ar' ? 'المهارات' : 'Skills' ?></h3>
                    <?php renderBulletedList($job['skills']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty(trim($job['qualifications'] ?? ''))): ?>
                <div class="job-section" style="margin-bottom: 0;">
                    <h3><?= $lang === 'ar' ? 'المؤهلات' : 'Qualifications' ?></h3>
                    <?php renderBulletedList($job['qualifications']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty(trim($job['experience'] ?? ''))): ?>
                <div class="job-section" style="margin-bottom: 0;">
                    <h3><?= $lang === 'ar' ? 'الخبرة' : 'Experience' ?></h3>
                    <?php renderBulletedList($job['experience']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty(trim($job['languages'] ?? ''))): ?>
                <div class="job-section" style="margin-bottom: 0;">
                    <h3><?= $lang === 'ar' ? 'اللغات' : 'Languages' ?></h3>
                    <p style="color: #555; font-size: 16px; margin: 0; padding-<?= $lang === 'ar' ? 'right' : 'left' ?>: 25px;">
                        <?= htmlspecialchars($job['languages']) ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty(trim($job['gender'] ?? ''))): ?>
                <div class="job-section" style="margin-bottom: 0;">
                    <h3><?= $lang === 'ar' ? 'الجنس' : 'Gender' ?></h3>
                    <p style="color: #555; font-size: 16px; margin: 0; padding-<?= $lang === 'ar' ? 'right' : 'left' ?>: 25px;">
                        <?= htmlspecialchars($job['gender']) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <div class="job-actions">
            <a href="apply.php?job_id=<?= $job['id'] ?>&lang=<?= $lang ?>" class="btn-primary">
                <?= $lang === 'ar' ? 'التقديم على الوظيفة' : 'Apply Now' ?>
            </a>
            <a href="<?= $lang === 'ar' ? 'career-ar.php' : 'career.php' ?>" class="btn-secondary">
                <?= $lang === 'ar' ? 'العودة للوظائف' : 'Back to Careers' ?>
            </a>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
