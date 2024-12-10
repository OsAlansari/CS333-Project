<?php
session_start();
include 'config.php'; // Include database connection

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

date_default_timezone_set('Asia/Bahrain');

try {
    // Fetch room and user details
    $stmt = $pdo->query("SELECT room_id, room_name FROM Rooms ORDER BY room_name ASC");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT id, email FROM users WHERE id != 0");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize variables
    $success_message = "";
    $error_message = "";

    // Handle booking submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book') {
        $room_name = trim($_POST['room_name']);
        $user_email = trim($_POST['user_email']);
        $date = $_POST['date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $purpose = htmlspecialchars($_POST['purpose']);

        $start_datetime = "$date $start_time";
        $end_datetime = "$date $end_time";

        // Validate inputs
        if (empty($room_name) || empty($user_email) || empty($date) || empty($start_time) || empty($end_time) || empty($purpose)) {
            $error_message = "All fields are required.";
        } elseif (strtotime($start_datetime) >= strtotime($end_datetime)) {
            $error_message = "Error: Start time must be earlier than end time.";
        } elseif (strtotime($start_time) < strtotime('08:00') || strtotime($end_time) > strtotime('20:00')) {
            $error_message = "Error: Booking slots are between 08:00 and 20:00.";
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

                // Check for booking conflicts
                $conflictStmt = $pdo->prepare("
                    SELECT COUNT(*) FROM Bookings 
                    WHERE room_id = ? 
                    AND ((start_time < ? AND end_time > ?))
                ");
                $conflictStmt->execute([$room_id, $end_datetime, $start_datetime]);
                $conflictCount = $conflictStmt->fetchColumn();

                if ($conflictCount > 0) {
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
    }

    // Fetch all bookings
    $bookingsStmt = $pdo->query("
        SELECT B.start_time, B.end_time, R.room_name, U.email 
        FROM Bookings B 
        JOIN Rooms R ON B.room_id = R.room_id
        JOIN users U ON B.user_id = U.id
        ORDER BY B.start_time
    ");
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
    <title>Add Booking</title>
    <link rel="stylesheet" href="../Css/admin_form.css">
</head>
<body>
    <div class="container">
        <h1>Book a Room</h1>

        <!-- Display messages -->
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Booking Form -->
        <h2>Book a Room</h2>
        <form method="POST">
            <input type="hidden" name="action" value="book">

            <label for="room_name">Room:</label>
            <select name="room_name" id="room_name" required>
                <option value="" disabled selected>Select a Room</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo htmlspecialchars($room['room_name']); ?>">
                        <?php echo htmlspecialchars($room['room_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="user_email">User Email:</label>
            <select name="user_email" id="user_email" required>
                <option value="" disabled selected>Select a User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo htmlspecialchars($user['email']); ?>">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="date">Date:</label>
            <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">

            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" id="start_time" required>

            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" id="end_time" required>

            <label for="purpose">Purpose:</label>
            <textarea name="purpose" id="purpose" required></textarea>

            <button class = "button" type="submit">Book Room</button>
        </form>

        <!-- Current Bookings -->
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bookings yet.</p>
            <?php endif; ?>
        </div>
        <form action="admin.php" method="get">
    <button class="button" type="submit">Admin Panel</button>
</form>
    </div>
</body>
</html>
