<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $room_id = intval($_POST['room_id']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if ($id && $room_id && $start_time && $end_time) {
        try {
            $stmt = $pdo->prepare("UPDATE bookings SET room_id = :room_id, start_time = :start_time, end_time = :end_time WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':room_id' => $room_id,
                ':start_time' => $start_time,
                ':end_time' => $end_time
            ]);
            $_SESSION['message'] = "Booking updated successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error updating booking: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid input! Ensure all fields are filled.";
    }

    header("Location: admin_schedule.php");
    exit();
}
?>
