<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<section class="career-section" style="margin-top:130px;" dir="rtl">
    <h2>الوظائف المتاحة</h2>

    <form method="GET" class="search-box" dir="rtl">
        <input type="text" name="search" placeholder="ابحث عن وظيفة..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">بحث</button>
    </form>

    <?php
    $search = '%' . ($_GET['search'] ?? '') . '%';

    $stmt = $conn->prepare("
        SELECT *
        FROM jobs
        WHERE (title_ar LIKE ? OR description_ar LIKE ?)
          AND publish_date <= CURDATE()
          AND (end_date IS NULL OR end_date >= CURDATE())
        ORDER BY created_at DESC
    ");

    $stmt->bind_param("ss", $search, $search);
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
                    📍 <?= htmlspecialchars($job['location']) ?> |
                    🕒 <?= htmlspecialchars($job['job_type']) ?>
                    <?php if (!empty($job['salary'])): ?>
                        | 💰 <?= htmlspecialchars($job['salary']) ?>
                    <?php endif; ?>
                </p>

                <p><?= htmlspecialchars(mb_substr($job['description_ar'], 0, 120)) ?>...</p>

                <a href="job-details.php?id=<?= $job['id'] ?>&lang=ar" class="apply-btn">
                    عرض التفاصيل
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'footer.php'; ?>