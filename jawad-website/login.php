<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = md5($_POST['password']);

    $query = $conn->query("SELECT * FROM admins WHERE username='$user' AND password='$pass'");

    if ($query->num_rows > 0) {
        $_SESSION['admin'] = $user;
        header("Location: career-admin.php");
        exit;
    } else {
        $error = "Invalid login";
    }
}
?>

<?php include 'header.php'; ?>

<section style="margin-top:120px; text-align:center;">
    <h2>Admin Login</h2>

    <form method="POST" class="login-form">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" class="careers-btn">Login</button>
    </form>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</section>

<?php include 'footer.php'; ?>