<?php
session_start();
include 'config.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login-register.php');
    exit();
}

// Fetch user type
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$type = $stmt->fetch();

if ($type['user_type'] != 'Admin') {
    header('Location: index.php');
    exit();
}

// Fetch all rooms for selection
$rooms = [];
try {
    $stmt = $pdo->query("SELECT room_id, room_name FROM Rooms");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching rooms: " . htmlspecialchars($e->getMessage()));
}

// Initialize variables for form processing
$success = false;
$message = "";
$room = null;

// Handle room selection
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_id'])) {
    $room_id = (int) $_GET['room_id'];

    // Fetch the room details for the selected room
    try {
        $stmt = $pdo->prepare("SELECT * FROM Rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            $message = "Room not found.";
        }
    } catch (Exception $e) {
        $message = "Error fetching room details: " . htmlspecialchars($e->getMessage());
    }
}

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $room_id = (int) $_POST['room_id'];
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
        // Attempt to update the room in the database
        try {
            $stmt = $pdo->prepare("UPDATE Rooms 
                                SET room_name = ?, room_type = ?, location = ?, capacity = ?, equipment = ? 
                                WHERE room_id = ?");
            $stmt->execute([$room_name, $room_type, $location, $capacity, $equipment, $room_id]);
            $success = true;
            $message = "Room updated successfully!";
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
    <title>Edit Room</title>
    <link rel="stylesheet" href="../Css/admin_form.css">
</head>
<body>
    <div class="container">
        <h1>Edit Room</h1>

        <?php if ($message): ?>
            <div class="message <?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Room Selection Form -->
        <form method="GET" action="edit_room.php">
            <label for="room_id">Select a Room:</label>
            <select id="room_id" name="room_id" required onchange="this.form.submit()">
                <option value="" disabled selected>Select Room</option>
                <?php foreach ($rooms as $r): ?>
                    <option value="<?= $r['room_id'] ?>" <?= (isset($room_id) && $room_id == $r['room_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['room_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit">Select</button></noscript>
        </form>

        <!-- Room Edit Form -->
        <?php if ($room): ?>
            <form method="POST" action="edit_room.php">
                <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">

                <label for="room_name">Room Name</label>
                <input type="text" id="room_name" name="room_name" value="<?= htmlspecialchars($room['room_name']) ?>" required>

                <label for="room_type">Room Type</label>
                <select id="room_type" name="room_type" required>
                    <option value="" disabled>Select Room Type</option>
                    <option value="Lab" <?= $room['room_type'] === 'Lab' ? 'selected' : '' ?>>Lab</option>
                    <option value="ClassRoom" <?= $room['room_type'] === 'ClassRoom' ? 'selected' : '' ?>>ClassRoom</option>
                </select>

                <label for="location">Location</label>
                <select id="location" name="location" required>
                    <option value="" disabled>Select a Location</option>
                    <option value="OpenLab" <?= $room['location'] === 'OpenLab' ? 'selected' : '' ?>>OpenLab</option>
                    <option value="IS" <?= $room['location'] === 'IS' ? 'selected' : '' ?>>IS</option>
                    <option value="CS" <?= $room['location'] === 'CS' ? 'selected' : '' ?>>CS</option>
                    <option value="CE" <?= $room['location'] === 'CE' ? 'selected' : '' ?>>CE</option>
                </select>

                <label for="capacity">Capacity</label>
                <input type="number" id="capacity" name="capacity" value="<?= htmlspecialchars($room['capacity']) ?>" min="1" required>

                <label for="equipment">Equipment</label>
                <textarea id="equipment" name="equipment" rows="4" required><?= htmlspecialchars($room['equipment']) ?></textarea>

                <button class = "button" type="submit">Update Room</button>
            </form>
        <?php endif; ?>
        </form>
    <form action="admin.php" method="get">
    <button class="button" type="submit">Admin Panel</button>
</form>
    </div>
</body>
</html>
