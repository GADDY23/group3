<?php
session_start();
include "connection.php";

// Check if the user is logged in and if the role is 'user'
if (!isset($_SESSION['loggeduser']) || $_SESSION['loggedrole'] !== 'user') {
    header("Location: user.php");
    exit();
}

$username = $_SESSION['loggeduser'];

// Ensure that 'loggeduser' exists in the session before using it
if (!isset($_SESSION['loggeduser'])) {
    echo "User is not logged in!";
    exit();
}

$stmt = $conn->prepare("SELECT id FROM userinfo WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

$appointments = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY date ASC, time ASC");
$appointments->bind_param("i", $user_id);
$appointments->execute();
$results = $appointments->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h3>My Appointments</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th><th>Time</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['time']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
       <a href="user.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>
