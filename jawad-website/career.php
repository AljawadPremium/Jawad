<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<section class="career-section" style="margin-top:130px;">
    <h2>Open Positions</h2>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search jobs..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>

    <?php
    $search = '%' . ($_GET['search'] ?? '') . '%';

    $stmt = $conn->prepare("
        SELECT *
        FROM jobs
        WHERE (title_en LIKE ? OR description_en LIKE ? OR title_ar LIKE ? OR description_ar LIKE ?)
          AND COALESCE(publish_date, created_at) <= CURDATE()
          AND (end_date IS NULL OR end_date >= CURDATE())
        ORDER BY created_at DESC
    ");

    $stmt->bind_param("ssss", $search, $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>No jobs available.</p>";
    }
    ?>

    <div class="job-list">
        <?php while ($job = $result->fetch_assoc()): ?>
            <div class="job-item">
                <h3><?= htmlspecialchars($job['title_en']) ?></h3>

                <p class="job-meta">
                    <?php $display_location = !empty($job['location_en']) ? $job['location_en'] : ($job['location'] ?? ''); ?>
                    📍 <?= htmlspecialchars($display_location) ?> |
                    🕒 <?= htmlspecialchars($job['job_type']) ?>
                    <?php if (!empty($job['salary'])): ?>
                        | 💰 <?= htmlspecialchars($job['salary']) ?>
                    <?php endif; ?>
                </p>

                <p><?= htmlspecialchars(substr($job['description_en'], 0, 120)) ?>...</p>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="job-details.php?id=<?= $job['id'] ?>" class="apply-btn">
                        View Details
                    </a>
                    <?php
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $host = $_SERVER['HTTP_HOST'];
                    $item_url = $protocol . $host . "/job-details.php?id=" . $job['id'];
                    $qr_thumb = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($item_url);
                    ?>
                    <img src="<?= $qr_thumb ?>" alt="QR"
                        style="width: 40px; height: 40px; border: 1px solid #eee; opacity: 0.7;"
                        title="Scan to view on mobile">
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'footer.php'; ?>