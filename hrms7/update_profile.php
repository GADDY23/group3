<?php
session_start();
include "connection.php";

// Redirect if user is not logged in
if (!isset($_SESSION['loggeduser'])) {
    header("Location: index.php");
    exit();
}

$current_username = $_SESSION['loggeduser'];
$message = "";

// Fetch current data
$stmt = $conn->prepare("SELECT fullname, gmail, contact, username FROM userinfo WHERE username = ?");
$stmt->bind_param("s", $current_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $fullname = $user['fullname'];
    $gmail = $user['gmail'];
    $contact = $user['contact'];
    $username = $user['username'];
} else {
    $message = "User not found.";
}

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_fullname = trim($_POST['fullname']);
    $new_gmail = trim($_POST['gmail']);
    $new_contact = trim($_POST['contact']);
    $new_username = trim($_POST['username']);
    $new_password = $_POST['password'];

    // Check if username is being changed and is already taken
    if ($new_username !== $current_username) {
        $check = $conn->prepare("SELECT username FROM userinfo WHERE username = ?");
        $check->bind_param("s", $new_username);
        $check->execute();
        $check_result = $check->get_result();
        if ($check_result->num_rows > 0) {
            $message = "Username already exists!";
        } else {
            $current_username = $new_username; // Allow update
        }
    }

    if (empty($message)) {
        if (!empty($new_password)) {
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE userinfo SET fullname = ?, gmail = ?, contact = ?, username = ?, password = ? WHERE username = ?");
            $update->bind_param("ssssss", $new_fullname, $new_gmail, $new_contact, $new_username, $hashedPassword, $_SESSION['loggeduser']);
        } else {
            $update = $conn->prepare("UPDATE userinfo SET fullname = ?, gmail = ?, contact = ?, username = ? WHERE username = ?");
            $update->bind_param("sssss", $new_fullname, $new_gmail, $new_contact, $new_username, $_SESSION['loggeduser']);
        }

        if ($update->execute()) {
            $_SESSION['loggeduser'] = $new_username;
            $message = "Profile updated successfully.";
            $fullname = $new_fullname;
            $gmail = $new_gmail;
            $contact = $new_contact;
            $username = $new_username;
        } else {
            $message = "Update failed: " . $update->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container p-5">
    <h2 class="mb-4">Update Profile</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($fullname); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Gmail</label>
            <input type="email" name="gmail" class="form-control" value="<?= htmlspecialchars($gmail); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($contact); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="user.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</body>
</html>
