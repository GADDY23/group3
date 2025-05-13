<?php
include "connection.php";
session_start();

// Only allow admin
if (!isset($_SESSION['loggedrole']) || $_SESSION['loggedrole'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Add Job
if (isset($_POST['add_job'])) {
    $title = $_POST['title'];
    $department = $_POST['department'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO job_listings (title, department, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $department, $description);
    $stmt->execute();
    $success = "Job added successfully.";
}

// Delete Job
if (isset($_GET['delete'])) {
    $job_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM job_listings WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $success = "Job deleted successfully.";
}

// Fetch all jobs
$jobs = $conn->query("SELECT * FROM job_listings");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Job Offers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container p-5">
    <h2>Manage Job Offers</h2>

    <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <!-- Add Job Form -->
    <form method="post" class="mb-4">
        <div class="mb-2">
            <input type="text" name="title" class="form-control" placeholder="Job Title" required>
        </div>
        <div class="mb-2">
            <input type="text" name="department" class="form-control" placeholder="Department" required>
        </div>
        <div class="mb-2">
            <textarea name="description" class="form-control" placeholder="Job Description" required></textarea>
        </div>
        <button type="submit" name="add_job" class="btn btn-primary">Add Job</button>
    </form>

    <!-- Job List -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Department</th>
                <th>Description</th>
                <th>Posted On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($job = $jobs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($job['title']) ?></td>
                    <td><?= htmlspecialchars($job['department']) ?></td>
                    <td><?= htmlspecialchars($job['description']) ?></td>
                    <td><?= htmlspecialchars($job['created_at']) ?></td>
                    <td>
                        <a href="?delete=<?= $job['job_id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                           <a href="deletejob.php?job_id=<?= $row['job_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>
