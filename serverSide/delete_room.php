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

// Initialize variables
$success = false;
$message = "";
$selected_room = null;

// Handle room selection
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_id'])) {
    $room_id = (int) $_GET['room_id'];

    // Fetch room details for the selected room
    try {
        $stmt = $pdo->prepare("SELECT * FROM Rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $selected_room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$selected_room) {
            $message = "Room not found.";
        }
    } catch (Exception $e) {
        $message = "Error fetching room details: " . htmlspecialchars($e->getMessage());
    }
}

// Handle room deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'])) {
    $room_id = (int) $_POST['room_id'];

    try {
        // Delete the room from the database
        $stmt = $pdo->prepare("DELETE FROM Rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);

        if ($stmt->rowCount() > 0) {
            $success = true;
            $message = "Room deleted successfully!";
            $selected_room = null; // Clear the selected room after deletion
        } else {
            $message = "Room not found or already deleted.";
        }
    } catch (Exception $e) {
        $message = "An error occurred: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Room</title>
    <link rel="stylesheet" href="../Css/admin_form.css">
</head>
<body>
    <div class="container">
        <h1>Delete Room</h1>

        <?php if ($message): ?>
            <div class="message <?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Room Selection Form -->
        <form method="GET" action="delete_room.php">
            <label for="room_id">Select a Room:</label>
            <select id="room_id" name="room_id" required onchange="this.form.submit()">
                <option value="" disabled selected>Select Room</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['room_id'] ?>" <?= (isset($room_id) && $room_id == $room['room_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['room_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button class = "button" type="submit">Select</button></noscript>
        </form>

        <!-- Show Room Details -->
        <?php if ($selected_room): ?>
            <div class="room-details">
                <h2>Room Details</h2>
                <p><strong>Name:</strong> <?= htmlspecialchars($selected_room['room_name']) ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($selected_room['room_type']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($selected_room['location']) ?></p>
                <p><strong>Capacity:</strong> <?= htmlspecialchars($selected_room['capacity']) ?></p>
                <p><strong>Equipment:</strong> <?= htmlspecialchars($selected_room['equipment']) ?></p>
            </div>

            <!-- Deletion Confirmation Form -->
            <form method="POST" action="delete_room.php">
                <input type="hidden" name="room_id" value="<?= $selected_room['room_id'] ?>">
                <button type="submit" class="button" style="background: linear-gradient(45deg, #ff5d5d, #4b0101);">Delete Room</button>
            </form>
        <?php endif; ?>
        </form>
    <form action="admin.php" method="get">
    <button class="button" type="submit">Admin Panel</button>
</form>
    </div>
</body>
</html>
