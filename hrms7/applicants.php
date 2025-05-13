<?php
session_start();
include "connection.php";

// Access control: only admin can view
if (!isset($_SESSION['loggeduser']) || $_SESSION['loggedrole'] !== "admin") {
    header("Location: ../../index.php");
    exit();
}

// Fetch all applications with user and job info
$sql = "
    SELECT a.application_id, u.username, jl.title AS job_title, jl.department, a.status
    FROM applications a
    JOIN userinfo u ON a.user_id = u.id
    JOIN job_listings jl ON a.job_id = jl.job_id
    ORDER BY a.application_id DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Applicants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">Manage Applicants</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Applicant</th>
                <th>Job Title</th>
                <th>Department</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['job_title']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td>
                        <?php
                            $status = htmlspecialchars($row['status']);
                            $badgeClass = match ($status) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default     => 'secondary',
                            };
                        ?>
                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                    </td>
                    <td>
                        <a href="update_status.php?id=<?= $row['application_id'] ?>&status=approved" class="btn btn-sm btn-success">Approve</a>
                        <a href="update_status.php?id=<?= $row['application_id'] ?>&status=rejected" class="btn btn-sm btn-danger">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No applications found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="admin.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</body>
</html>
