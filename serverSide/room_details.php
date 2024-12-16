<?php
session_start();
include 'config.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: ../serverSide/login-register.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Room ID is required.");
}

// Sanitize and validate the input
$room_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

date_default_timezone_set('Asia/Bahrain');

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

        // Ensure the booking date is not today or earlier
        $current_date = new DateTime();
        $booking_date = new DateTime($date);

        if ($booking_date <= $current_date->setTime(0, 0, 0)) {
            $error_message = "Error: You can only book for upcoming dates.";
        } elseif (strtotime($start_datetime) >= strtotime($end_datetime)) {
            $error_message = "Error: Start time must be earlier than end time.";
        }elseif (strtotime($start_time) < strtotime('08:00') || strtotime($end_time) > strtotime('20:00')) {
            $error_message = "Error: Booking slots are between 08:00 and 20:00.";
        }else {
            // Check for booking conflicts
            $conflictStmt = $pdo->prepare("
                SELECT COUNT(*) FROM Bookings 
                WHERE room_id = ? 
                AND ((start_time < ? AND end_time > ?))
            ");
            $conflictStmt->execute([$room_id, $end_datetime, $start_datetime]);
            $conflictCount = $conflictStmt->fetchColumn();

            // Fetch user type
            $stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (($user['user_type'] == 'Student' && $room['location'] == 'OpenLab') || ($user['user_type'] == 'Admin')) {
                $error_message = "You are not allowed to book this room.";
            } elseif ($conflictCount > 0) {
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
    }

    // Fetch all bookings for this room
    $bookingsStmt = $pdo->prepare("
        SELECT start_time, end_time 
        FROM Bookings 
        WHERE room_id = ?
        ORDER BY start_time
    ");
    $bookingsStmt->execute([$room_id]);
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
        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> attendees</p>
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
            <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" id="start_time" required min="08:00">
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" id="end_time" required max="20:00">
            <label for="purpose">Purpose:</label>
            <textarea name="purpose" id="purpose" required></textarea>
            <button class="button" type="submit">Book Room</button>
        </form>

        <!-- Current Bookings -->
        <h2>Bookings for this room</h2>
        <div class="bookings">
            <?php if (!empty($bookings)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['end_time']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bookings for this room yet.</p>
            <?php endif; ?>
            <form action="room_browse.php" method="get">
    <button class="button" type="submit">Room Browsing</button>
</form>
            <form action="index.php" method="get">
    <button class="button" type="submit">Home Page</button>
</form>
        </div>
    </div>
</body>
</html>
