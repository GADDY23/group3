<?php
session_start();
include "connection.php";

if (!isset($_SESSION['loggeduser']) || $_SESSION['loggedrole'] !== "admin") {
    header("Location: ../../admin.php");
    exit();
}

$job_id = $_GET['id'] ?? null;
if ($job_id) {
    $stmt = $conn->prepare("DELETE FROM job_listings WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
}

header("Location: manage_jobs.php");
exit();
?>
