<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) { die("Invalid ID"); }

// Handle Update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE jobs SET title_en=?, title_ar=?, description_en=?, description_ar=? WHERE id=?");
    $stmt->bind_param("ssssi", $_POST['title_en'], $_POST['title_ar'], $_POST['description_en'], $_POST['description_ar'], $id);
    $stmt->execute();
    header("Location: career-admin.php?updated=1");
    exit;
}

// Fetch current data
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
include 'header.php';
?>

<section class="career-admin">
    <h2>Edit Job</h2>
    <div class="admin-card">
        <form method="POST" class="admin-form">
            <div class="field">
                <label>Title (EN)</label>
                <input type="text" name="title_en" value="<?= htmlspecialchars($job['title_en']) ?>" required>
            </div>
            <div class="field">
                <label>Title (AR)</label>
                <input type="text" name="title_ar" value="<?= htmlspecialchars($job['title_ar']) ?>" required>
            </div>
            <div class="field full">
                <label>Description (EN)</label>
                <textarea name="description_en" required><?= htmlspecialchars($job['description_en']) ?></textarea>
            </div>
            <div class="field full">
                <label>Description (AR)</label>
                <textarea name="description_ar" required><?= htmlspecialchars($job['description_ar']) ?></textarea>
            </div>
            <button type="submit" class="admin-btn">Update Job</button>
            <a href="career-admin.php">Cancel</a>
        </form>
    </div>
</section>

<?php include 'footer.php'; ?>
