<?php
session_start();
include 'config.php'; // Include database connection

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../serverSide/login-register.php');
    exit();
}

// Fetch user type
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$type = $stmt->fetch();

if ($type['user_type'] != 'Admin') {
    header('Location: ../serverSide/index.php');
    exit();
}

// Initialize variables for form processing
$success = false;
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $room_name = trim($_POST['room_name'] ?? '');
    $room_type = trim($_POST['room_type'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $capacity = (int) ($_POST['capacity'] ?? 0);
    $equipment = trim($_POST['equipment'] ?? '');

    // Validate input
    $valid_locations = ['IS', 'CS', 'CE', 'OpenLab'];
    if (empty($room_name) || empty($room_type) || empty($location) || $capacity <= 0) {
        $message = "All fields are required and must be valid.";
    } elseif (!in_array($location, $valid_locations)) {
        $message = "Invalid location selected.";
    } else {
        // Attempt to add room to the database
        try {
            $stmt = $pdo->prepare("INSERT INTO Rooms (room_name, room_type, location, capacity, equipment) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$room_name, $room_type, $location, $capacity, $equipment]);
            $success = true;
            $message = "Room added successfully!";
        } catch (Exception $e) {
            $message = "An error occurred: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link rel="stylesheet" href="../Css/admin_form.css">
</head>
<body>
    <div class="container">
    <h1>Add Room</h1>

    <?php if ($message): ?>
        <div class="message <?= $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="add_room.php">
        <label for="room_name">Room Name</label>
        <input type="text" id="room_name" name="room_name" required>

        <label for="room_type">Room Type</label>
        <select id="room_type" name="room_type" required>
        <option value="" disabled selected>Select Room Type</option>
            <option value="Lab">Lab</option>
            <option value="ClassRoom">ClassRoom</option>
        </select>

        <label for="location">Location</label>
        <select id="location" name="location" required>
            <option value="" disabled selected>Select a Location</option>
            <option value="OpenLab">OpenLab</option>
            <option value="IS">IS</option>
            <option value="CS">CS</option>
            <option value="CE">CE</option>
        </select>

        <label for="capacity">Capacity</label>
        <input type="number" id="capacity" name="capacity" min="1" required>

        <label for="equipment">Equipment</label>
        <textarea id="equipment" name="equipment" rows="4" placeholder="E.g., Projector, Whiteboard"></textarea>

        <button class = "button" type="submit">Add Room</button>
    </form>
    <form action="admin.php" method="get">
    <button class="button" type="submit">Admin Panel</button>
</form>
    </div>
</body>
</html>
