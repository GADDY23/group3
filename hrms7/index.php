<?php
include "connection.php";
session_start();

// Create default user (only if not exists)
$defaultUsers = [
    ['username' => 'user1', 'password' => 'user123', 'role' => 'user'],
    ['username' => 'admin1', 'password' => 'admin123', 'role' => 'admin']
];

foreach ($defaultUsers as $user) {
    $check = $conn->prepare("SELECT * FROM userinfo WHERE username = ?");
    $check->bind_param("s", $user['username']);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO userinfo (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user['username'], $hashedPassword, $user['role']);
        $stmt->execute();
    }
}

// Handle login
if (isset($_POST['login'])) {
    $getuser = $_POST['username'];
    $getpass = $_POST['password'];

    $getlogin = $conn->prepare("SELECT username, password, role FROM userinfo WHERE username = ?");
    $getlogin->bind_param("s", $getuser);
    $getlogin->execute();
    $result = $getlogin->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($getpass, $row['password'])) {
            $_SESSION['loggeduser'] = $row['username'];
            $_SESSION['loggedrole'] = $row['role'];

            // Redirect to dashboard based on role
            if ($row['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login Page</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="submit" name="login" value="Login">
    </form>

    <p>If you don't have an account, <a href="register.php">register here</a>.</p>
</body>
</html>
