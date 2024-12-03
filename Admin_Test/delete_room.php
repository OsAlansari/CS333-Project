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
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['message'] = "Room deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting room: " . $e->getMessage();
    }
}

header("Location: admin_room_management.php");
exit();
?>
