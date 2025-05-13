<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['loggeduser']) && isset($_SESSION['loggedrole'])) {
    if ($_SESSION['loggedrole'] === "user" || $_SESSION['loggedrole'] === "admin") {
        $username = $_SESSION['loggeduser'];
    } else {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container p-5">
    <h1 class="mb-4">Welcome, <?= htmlspecialchars($username); ?>!</h1>
    <a href="jobs.php" class="btn btn-primary">View Job Listings</a>
    <a href="my_applications.php" class="btn btn-info">My Applications</a>

    <a href="update_profile.php" class="btn btn-warning me-2">Update Profile</a>
    <a href="index.php" class="btn btn-danger">Logout</a>
</body>
</html>
