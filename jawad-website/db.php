<?php
$host = "db";              // Always localhost on HostGator
$user = "aljawad_user";    // Replace with your actual MySQL user
$pass = "strongpass";              // Replace with your password
$dbname = "aljawad";     // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
