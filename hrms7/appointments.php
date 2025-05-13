<?php
session_start();
include "connection.php";

// Only admin access
if (!isset($_SESSION['loggeduser']) || $_SESSION['loggedrole'] !== "admin") {
    header("Location: ../../index.php");
    exit();
}

// Handle appointment submission
if (isset($_POST['submit'])) {
    $user_id = $_POST['user_id'];
    $job_id = $_POST['job_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, job_id, date, time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $job_id, $date, $time);
    $stmt->execute();
}

// Fetch applicants who applied
$applicants = $conn->query("
    SELECT DISTINCT u.id, u.username 
    FROM applications a 
    JOIN userinfo u ON a.user_id = u.id
");

// Fetch job listings
$jobs = $conn->query("SELECT job_id, title FROM job_listings");

// Fetch appointments
$appointments = $conn->query("
    SELECT ap.*, u.username, j.title 
    FROM appointments ap 
    JOIN userinfo u ON ap.user_id = u.id 
    JOIN job_listings j ON ap.job_id = j.job_id 
    ORDER BY ap.date, ap.time
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">Set Interview Appointments</h2>

    <form method="POST" class="row g-3 mb-5">
        <div class="col-md-3">
            <label class="form-label">Applicant</label>
            <select name="user_id" class="form-select" required>
                <option value="">Select Applicant</option>
                <?php while ($row = $applicants->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['username']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Job Title</label>
            <select name="job_id" class="form-select" required>
                <option value="">Select Job</option>
                <?php while ($job = $jobs->fetch_assoc()): ?>
                    <option value="<?= $job['job_id'] ?>"><?= htmlspecialchars($job['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">Time</label>
            <input type="time" name="time" class="form-control" required>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" name="submit" class="btn btn-primary w-100">Set Appointment</button>
        </div>
    </form>

    <h4>Scheduled Appointments</h4>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Applicant</th>
                <th>Job Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($appointments->num_rows > 0): ?>
                <?php while ($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['status']) ?></span></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No appointments scheduled.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="admin.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</body>
</html>
