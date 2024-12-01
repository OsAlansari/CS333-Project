<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['timeslot_id'])) {
    die('Timeslot ID is required.');
}

$timeslot_id = $_GET['timeslot_id'];

// Fetch timeslot details
$stmt = $pdo->prepare("SELECT * FROM room_timeslots WHERE id = ?");
$stmt->execute([$timeslot_id]);
$timeslot = $stmt->fetch();

if (!$timeslot) {
    die('Timeslot not found.');
}

// Check if timeslot is already booked
if ($timeslot['is_booked']) {
    die('This timeslot is already booked.');
}

// Book the room by updating the timeslot to booked
$stmt = $pdo->prepare("UPDATE room_timeslots SET is_booked = 1 WHERE id = ?");
$stmt->execute([$timeslot_id]);

echo 'Room booked successfully!';
?>
