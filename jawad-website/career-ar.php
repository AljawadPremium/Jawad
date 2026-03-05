<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<section class="career-section" style="margin-top:130px;" dir="rtl">
    <h2>الوظائف المتاحة</h2>

    <form method="GET" class="search-box" dir="rtl">
        <input type="text" name="search" placeholder="ابحث عن وظيفة..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">بحث</button>
    </form>

    <?php
    $search = '%' . ($_GET['search'] ?? '') . '%';

    $stmt = $conn->prepare("
        SELECT *
        FROM jobs
        WHERE (title_ar LIKE ? OR description_ar LIKE ? OR title_en LIKE ? OR description_en LIKE ?)
          AND COALESCE(publish_date, created_at) <= CURDATE()
          AND (end_date IS NULL OR end_date >= CURDATE())
        ORDER BY created_at DESC
    ");

    $stmt->bind_param("ssss", $search, $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='text-align:center;'>لا يوجد وظائف حالياً.</p>";
    }
    ?>

    <div class="job-list" dir="rtl">
        <?php while ($job = $result->fetch_assoc()): ?>
            <div class="job-item">
                <h3><?= htmlspecialchars($job['title_ar']) ?></h3>

                <p class="job-meta">
                    <?php
                    $type_map = [
                        'Full-time' => 'دوام كامل',
                        'Part-time' => 'دوام جزئي',
                        'Contract' => 'عقد',
                        'Remote' => 'عمل عن بعد',
                        'Internship' => 'تدريب'
                    ];
                    $display_type = $type_map[$job['job_type']] ?? $job['job_type'];
                    $display_location = !empty($job['location']) ? $job['location'] : ($job['location_en'] ?? '');
                    ?>
                    📍 <?= htmlspecialchars($display_location) ?> |
                    🕒 <?= htmlspecialchars($display_type) ?>
                    <?php if (!empty($job['salary'])): ?>
                        | 💰 <?= htmlspecialchars($job['salary']) ?>
                    <?php endif; ?>
                </p>

                <p><?= htmlspecialchars(mb_substr($job['description_ar'], 0, 120)) ?>...</p>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="job-details.php?id=<?= $job['id'] ?>&lang=ar" class="apply-btn">
                        عرض التفاصيل
                    </a>
                    <?php
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $host = $_SERVER['HTTP_HOST'];
                    $item_url = $protocol . $host . "/job-details.php?id=" . $job['id'];
                    $qr_thumb = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($item_url);
                    ?>
                    <img src="<?= $qr_thumb ?>" alt="QR"
                        style="width: 40px; height: 40px; border: 1px solid #eee; opacity: 0.7;"
                        title="امسح الرمز للعرض على الجوال">
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'footer.php'; ?>