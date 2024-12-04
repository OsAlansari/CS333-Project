<?php
session_start();
include 'config.php'; // Include the database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: /login-register.php'); // Redirect to login page
    exit();
}
$user_id = $_SESSION['user_id'];

function add_booking($pdo, $user_id, $room_id, $start_time, $end_time, $purpose, $created_at) {
    try {
        $sql = "INSERT INTO bookings (user_id, room_id, start_time, end_time, purpose, created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $room_id, $start_time, $end_time, $purpose, $created_at]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $purpose = $_POST['purpose'];
    $created_at = date('Y-m-d H:i:s', time()); // Format the current time as SQL TIMESTAMP
    add_booking($pdo, $user_id, $room_id, $start_time, $end_time, $purpose, $created_at);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Booking</title>
    <link rel="stylesheet" href="../Css/room_browse.css">
    <script src="../Script/room_browse.js" defer></script>
</head>

<body>
    <div class="main">
        <div class="booking-container">
            <div class="title">Lab Booking</div>
            <div class="building-selector">
                <div class="building" data-building="1">S40 (1)</div>
                <div class="building" data-building="2">S40 (2)</div>
                <div class="building" data-building="3">S40 (3)</div>
            </div>

            <div class="lab-selector hidden">
                <div class="status">
                    <div class="item">Available</div>
                    <div class="item">Booked</div>
                    <div class="item">Selected</div>
                </div>
                <div class="all-labs">
                </div>
            </div>

            <div class="booking-form hidden">
                <form id="bookingForm">
                    <label for="username">Name:</label>
                    <input type="text" id="username" required>
                    <label for="date">Date:</label>
                    <input type="date" id="date" required>
                    <label for="time">Time:</label>
                    <input type="time" id="time" required>
                    <button type="submit">Book</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>