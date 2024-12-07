<?php
session_start();
include 'config.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: /login-register.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Room ID is required.");
}

// Sanitize and validate the input
$room_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

try {
    // Fetch room details
    $stmt = $pdo->prepare("SELECT room_name, room_type, location, capacity, equipment FROM Rooms WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        die("Room not found.");
    }

    // Handle booking submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book') {
        $date = $_POST['date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $purpose = htmlspecialchars($_POST['purpose']);

        $start_datetime = "$date $start_time";
        $end_datetime = "$date $end_time";

        // Check for booking conflicts
        $conflictStmt = $pdo->prepare("
            SELECT * FROM Bookings 
            WHERE room_id = ? 
            AND ((start_time <= ? AND end_time > ?))
        ");
        $conflictStmt->execute([$room_id, $start_datetime, $end_datetime]);

        if ($conflictStmt->rowCount() > 0) {
            $error_message = "Conflict: The selected timeslot is already booked.";
        } else {
            // Insert booking
            $insertStmt = $pdo->prepare("
                INSERT INTO Bookings (user_id, room_id, start_time, end_time, purpose, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $insertStmt->execute([$user_id, $room_id, $start_datetime, $end_datetime, $purpose]);
            $success_message = "Booking successful!";
        }
    }

    // Handle booking cancellation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
        $booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);

        $deleteStmt = $pdo->prepare("DELETE FROM Bookings WHERE booking_id = ? AND user_id = ?");
        $deleteStmt->execute([$booking_id, $user_id]);

        if ($deleteStmt->rowCount() > 0) {
            $success_message = "Booking successfully canceled.";
        } else {
            $error_message = "Unable to cancel booking or insufficient permissions.";
        }
    }

    // Fetch all bookings for this room by the user
    $bookingsStmt = $pdo->prepare("
        SELECT * FROM Bookings 
        WHERE room_id = ?  AND user_id = ?
        ORDER BY start_time
    ");
    $bookingsStmt->execute([$room_id, $user_id]);
    $bookings = $bookingsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link rel="stylesheet" href="../Css/room_details.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($room['room_name']); ?> Details</h1>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($room['location']); ?></p>
        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?></p>
        <p><strong>Equipment:</strong> <?php echo htmlspecialchars($room['equipment']); ?></p>

        <!-- Display messages -->
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Booking Form -->
        <h2>Book This Room</h2>
        <form method="POST">
            <input type="hidden" name="action" value="book">
            <label for="date">Date:</label>
            <input type="date" name="date" id="date" required>
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" id="start_time" required>
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" id="end_time" required>
            <label for="purpose">Purpose:</label>
            <textarea name="purpose" id="purpose" required></textarea>
            <button type="submit">Book Room</button>
        </form>

        <!-- Current Bookings -->
        <h2>Your bookings for this room</h2>
        <div class="bookings">
            <?php if (!empty($bookings)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Purpose</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['end_time']))); ?></td>
                                <td><?php echo htmlspecialchars($booking['purpose']); ?></td>
                                <td>
                                    <?php if ($booking['user_id'] === $user_id): ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                            <button type="submit">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No bookings for this room yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
