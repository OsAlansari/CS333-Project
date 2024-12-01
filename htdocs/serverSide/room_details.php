<?php
include 'config.php';

if (!isset($_GET['id'])) {
    die('Room ID is required.');
}

$room_id = $_GET['id'];

// Fetch room details
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    die('Room not found.');
}

// Fetch available timeslots
$stmt = $pdo->prepare("SELECT * FROM room_timeslots WHERE room_id = ? AND is_booked = 0 ORDER BY start_time");
$stmt->execute([$room_id]);
$timeslots = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
</head>
<body>
    <h1>Room Details: <?= htmlspecialchars($room['name']) ?></h1>
    
    <p><strong>Location:</strong> <?= htmlspecialchars($room['location']) ?></p>
    <p><strong>Capacity:</strong> <?= htmlspecialchars($room['capacity']) ?> people</p>
    <p><strong>Description:</strong> <?= htmlspecialchars($room['description']) ?></p>
    <p><strong>Equipment:</strong> <?= htmlspecialchars($room['equipment']) ?></p>

    <h3>Available Timeslots</h3>
    <ul>
        <?php foreach ($timeslots as $timeslot): ?>
            <li>
                <strong><?= date('Y-m-d H:i', strtotime($timeslot['start_time'])) ?></strong> to 
                <strong><?= date('Y-m-d H:i', strtotime($timeslot['end_time'])) ?></strong>
                <a href="book_room.php?timeslot_id=<?= $timeslot['id'] ?>">Book this timeslot</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="room_browse.php">Back to Room List</a>
</body>
</html>
