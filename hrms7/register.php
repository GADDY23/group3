<?php
include "connection.php";
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $gmail = trim($_POST["gmail"]);
    $contact = trim($_POST["contact"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Check if user already exists
    $check = $conn->prepare("SELECT * FROM userinfo WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "Username already exists!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO userinfo (fullname, gmail, contact, username, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $gmail, $contact, $username, $hashedPassword, $role);

        if ($stmt->execute()) {
            $message = "Registration successful. <a href='index.php'>Login now</a>";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container p-5">
    <h2>User Registration</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="mb-3">
            <input type="email" name="gmail" class="form-control" placeholder="Gmail" required>
        </div>
        <div class="mb-3">
            <input type="text" name="contact" class="form-control" placeholder="Contact Number" required>
        </div>
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-3">
            <select name="role" class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="index.php" class="btn btn-secondary">Go to Login</a>
    </form>
</body>
</html>

