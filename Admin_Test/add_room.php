<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $capacity = intval($_POST['capacity']);
    $equipment = trim($_POST['equipment']);

    if ($name && $capacity > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO rooms (name, capacity, equipment) VALUES (:name, :capacity, :equipment)");
            $stmt->execute([
                ':name' => $name,
                ':capacity' => $capacity,
                ':equipment' => $equipment
            ]);
            $_SESSION['message'] = "Room added successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error adding room: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid input! Ensure all fields are filled correctly.";
    }

    header("Location: admin_room_management.php");
    exit();
}
?>
