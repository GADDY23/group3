<?php
session_start();
include "connection.php";

// Check if user is logged in
if (!isset($_SESSION['loggeduser']) || !isset($_SESSION['loggedrole'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['loggeduser'];

// Fetch user ID
$stmt = $conn->prepare("SELECT id FROM userinfo WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Handle application submission
if (isset($_GET['apply']) && is_numeric($_GET['apply'])) {
    $job_id = $_GET['apply'];

    // Check if already applied
    $check = $conn->prepare("SELECT * FROM applications WHERE user_id = ? AND job_id = ?");
    $check->bind_param("ii", $user_id, $job_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows == 0) {
        $apply = $conn->prepare("INSERT INTO applications (user_id, job_id, status) VALUES (?, ?, 'pending')");
        $apply->bind_param("ii", $user_id, $job_id);
        $apply->execute();
        $message = "Application submitted.";
    } else {
        $message = "You already applied for this job.";
    }
}

// Fetch job listings
$jobs = $conn->query("SELECT * FROM job_listings WHERE status = 'open'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container p-5">
    <h2>Available Job Listings</h2>
    
    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Department</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $jobs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= htmlspecialchars($row['department']); ?></td>
                    <td><a href="?apply=<?= $row['job_id']; ?>" class="btn btn-success btn-sm">Apply</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="user.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>
