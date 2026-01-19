<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<section class="career-section" style="margin-top:130px;">
    <h2>Open Positions</h2>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search jobs..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>

    <?php
    $search = '%' . ($_GET['search'] ?? '') . '%';

    $stmt = $conn->prepare("
        SELECT *
        FROM jobs
        WHERE (title_en LIKE ? OR description_en LIKE ?)
          AND publish_date <= CURDATE()
          AND (end_date IS NULL OR end_date >= CURDATE())
        ORDER BY created_at DESC
    ");

    $stmt->bind_param("ss", $search, $search);
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
                    📍 <?= htmlspecialchars($job['location']) ?> |
                    🕒 <?= htmlspecialchars($job['job_type']) ?>
                    <?php if (!empty($job['salary'])): ?>
                        | 💰 <?= htmlspecialchars($job['salary']) ?>
                    <?php endif; ?>
                </p>

                <p><?= htmlspecialchars(substr($job['description_en'], 0, 120)) ?>...</p>

                <a href="job-details.php?id=<?= $job['id'] ?>" class="apply-btn">
                    View Details
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'footer.php'; ?>