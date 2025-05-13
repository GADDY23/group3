<?php
session_start();
include "connection.php";

if (!isset($_SESSION['loggeduser']) || $_SESSION['loggedrole'] !== "admin") {
    header("Location: ../../index.php");
    exit();
}

if (isset($_GET['id'], $_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];

    if (in_array($status, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
}

header("Location: applicants.php");
exit();
