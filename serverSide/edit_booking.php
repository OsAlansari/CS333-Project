<?php
session_start();
include 'config.php'; // Include database connection

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

date_default_timezone_set('Asia/Bahrain');

try {
    // Fetch all bookings for dropdown
    $stmt = $pdo->query("
        SELECT B.booking_id, R.room_name, U.email, B.start_time, B.end_time, B.purpose 
        FROM Bookings B
        JOIN Rooms R ON B.room_id = R.room_id
        JOIN users U ON B.user_id = U.id
        ORDER BY B.start_time
    ");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all rooms and users
    $rooms = $pdo->query("SELECT room_id, room_name FROM Rooms ORDER BY room_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $users = $pdo->query("SELECT id, email FROM users WHERE id != 0")->fetchAll(PDO::FETCH_ASSOC);

    // Initialize variables
    $success_message = "";
    $error_message = "";
    $selected_booking = null;

    // Handle booking selection
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['booking_id'])) {
        $booking_id = (int) $_GET['booking_id'];

        $stmt = $pdo->prepare("
            SELECT B.*, R.room_name, U.email 
            FROM Bookings B
            JOIN Rooms R ON B.room_id = R.room_id
            JOIN users U ON B.user_id = U.id
            WHERE B.booking_id = ?
        ");
        $stmt->execute([$booking_id]);
        $selected_booking = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Handle booking edit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
        $booking_id = (int) $_POST['booking_id'];
        $room_name = trim($_POST['room_name']);
        $user_email = trim($_POST['user_email']);
        $date = trim($_POST['date']);
        $start_time = trim($_POST['start_time']);
        $end_time = trim($_POST['end_time']);
        $purpose = htmlspecialchars($_POST['purpose']);

        $start_datetime = "$date $start_time";
        $end_datetime = "$date $end_time";

        // Validate inputs
        if (empty($room_name) || empty($user_email) || empty($date) || empty($start_time) || empty($end_time) || empty($purpose)) {
            $error_message = "All fields are required.";
        } elseif (strtotime($start_datetime) >= strtotime($end_datetime)) {
            $error_message = "Error: Start time must be earlier than end time.";
        } else {
            // Fetch room_id and user_id
            $stmt = $pdo->prepare("SELECT room_id FROM Rooms WHERE room_name = ?");
            $stmt->execute([$room_name]);
            $room = $stmt->fetch();

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$user_email]);
            $user = $stmt->fetch();

            if (!$room || !$user) {
                $error_message = "Invalid room or user.";
            } else {
                $room_id = $room['room_id'];
                $user_id = $user['id'];

                // Update the booking
                $stmt = $pdo->prepare("
                    UPDATE Bookings 
                    SET room_id = ?, user_id = ?, start_time = ?, end_time = ?, purpose = ? 
                    WHERE booking_id = ?
                ");
                $stmt->execute([$room_id, $user_id, $start_datetime, $end_datetime, $purpose, $booking_id]);
                $success_message = "Booking updated successfully!";
                $selected_booking = null; // Clear the selection after update
            }
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="../Css/admin_form.css">
</head>
<body>
    <div class="container">
        <h1>Edit Booking</h1>

        <!-- Display messages -->
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Booking Selection -->
        <form method="GET" action="edit_booking.php">
            <label for="booking_id">Select Booking:</label>
            <select name="booking_id" id="booking_id" required onchange="this.form.submit()">
                <option value="" disabled selected>Select a Booking</option>
                <?php foreach ($bookings as $booking): ?>
                    <option value="<?php echo htmlspecialchars($booking['booking_id']); ?>" 
                        <?php echo (isset($selected_booking) && $selected_booking['booking_id'] == $booking['booking_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($booking['room_name']); ?> - <?php echo htmlspecialchars($booking['email']); ?> - 
                        <?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['start_time']))); ?> 
                        <?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))); ?> to 
                        <?php echo htmlspecialchars(date('H:i', strtotime($booking['end_time']))); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Show Booking Details -->
        <?php if ($selected_booking): ?>
            <form method="POST" action="edit_booking.php">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($selected_booking['booking_id']); ?>">

                <label for="room_name">Room:</label>
                <select name="room_name" id="room_name" required>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo htmlspecialchars($room['room_name']); ?>" 
                            <?php echo ($room['room_name'] === $selected_booking['room_name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($room['room_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="user_email">User Email:</label>
                <select name="user_email" id="user_email" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['email']); ?>" 
                            <?php echo ($user['email'] === $selected_booking['email']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['email']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="date">Date:</label>
                <input type="date" name="date" id="date" required 
                    value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($selected_booking['start_time']))); ?>">

                <label for="start_time">Start Time:</label>
                <input type="time" name="start_time" id="start_time" required 
                    value="<?php echo htmlspecialchars(date('H:i', strtotime($selected_booking['start_time']))); ?>">

                <label for="end_time">End Time:</label>
                <input type="time" name="end_time" id="end_time" required 
                    value="<?php echo htmlspecialchars(date('H:i', strtotime($selected_booking['end_time']))); ?>">

                <label for="purpose">Purpose:</label>
                <textarea name="purpose" id="purpose" required><?php echo htmlspecialchars($selected_booking['purpose']); ?></textarea>

                <button type="submit">Update Booking</button>
            </form>
        <?php endif; ?>

        <!-- Display All Bookings -->
        <h2>All Bookings</h2>
        <div class="bookings">
            <?php if (!empty($bookings)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>User Email</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['end_time']))); ?></td>
                                <td><?php echo htmlspecialchars($booking['purpose']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bookings yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
