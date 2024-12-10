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

    // Handle delete booking
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
        $booking_id = (int) $_POST['booking_id'];

        if ($booking_id <= 0) {
            $error_message = "Invalid booking selection.";
        } else {
            // Delete the booking
            $stmt = $pdo->prepare("DELETE FROM Bookings WHERE booking_id = ?");
            $stmt->execute([$booking_id]);

            if ($stmt->rowCount() > 0) {
                $success_message = "Booking deleted successfully!";
                $selected_booking = null; // Clear the selection
            } else {
                $error_message = "Error: Booking not found.";
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
    <title>Delete Booking</title>
    <link rel="stylesheet" href="../Css/admin_form.css">
</head>
<body>
    <div class="container">
        <h1>Delete Booking</h1>

        <!-- Display messages -->
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Booking Selection -->
        <form method="GET" action="delete_booking.php">
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
            <div class="booking-details">
                <h2>Booking Details</h2>
                <p><strong>Room:</strong> <?php echo htmlspecialchars($selected_booking['room_name']); ?></p>
                <p><strong>User Email:</strong> <?php echo htmlspecialchars($selected_booking['email']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars(date('Y-m-d', strtotime($selected_booking['start_time']))); ?></p>
                <p><strong>Start Time:</strong> <?php echo htmlspecialchars(date('H:i', strtotime($selected_booking['start_time']))); ?></p>
                <p><strong>End Time:</strong> <?php echo htmlspecialchars(date('H:i', strtotime($selected_booking['end_time']))); ?></p>
                <p><strong>Purpose:</strong> <?php echo htmlspecialchars($selected_booking['purpose']); ?></p>

                <!-- Confirm Deletion -->
                <form method="POST" action="delete_booking.php">
                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($selected_booking['booking_id']); ?>">
                    <button type="submit" class="button" style="background: linear-gradient(45deg, #ff5d5d, #4b0101);">Delete Booking</button>
                </form>
            </div>
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
        </form>
    <form action="admin.php" method="get">
    <button class="button" type="submit">Admin Panel</button>
</form>
    </div>
</body>
</html>
