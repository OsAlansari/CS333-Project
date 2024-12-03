<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$id = intval($_GET['id']);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['message'] = "Booking deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting booking: " . $e->getMessage();
    }
}

header("Location: admin_schedule.php");
exit();
?>
