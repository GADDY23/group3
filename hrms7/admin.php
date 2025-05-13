<?php
session_start();

// Access control: only admin can view
if (!isset($_SESSION['loggeduser']) || $_SESSION['loggedrole'] !== "admin") {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h1 class="mb-4">Admin Dashboard</h1>
    <p>Welcome, <strong><?= htmlspecialchars($_SESSION['loggeduser']) ?></strong></p>

    <div class="list-group mb-4">
    <a href="manage_jobs.php" class="list-group-item list-group-item-action">📝 Manage Job Offers</a>
    <a href="applicants.php" class="list-group-item list-group-item-action">👥 Manage Applicants</a>
    <a href="id_system.php" class="list-group-item list-group-item-action">🆔 Applicant ID System</a>
        <a href="appointments.php" class="list-group-item list-group-item-action">📝 set of appointments</a>

</div>


<a href="index.php" class="btn btn-danger">Logout</a></body>
</html>
