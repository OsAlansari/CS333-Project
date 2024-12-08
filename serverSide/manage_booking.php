<?php 
// Start the session
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
include 'config.php';

if (!$isLoggedIn) {
    header('Location: login-register.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['user_type'] == 'Admin') {
    header('Location: index.php');
    exit();
}

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);

    // Ensure the booking belongs to the logged-in user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $isValid = $stmt->fetchColumn();

    if ($isValid) {
        // Proceed with deletion
        $deleteStmt = $pdo->prepare("DELETE FROM Bookings WHERE booking_id = ?");
        $deleteStmt->execute([$booking_id]);

        if ($deleteStmt->rowCount() > 0) {
            $success_message = "Booking successfully canceled.";
        } else {
            $error_message = "Error: Unable to cancel booking.";
        }
    } else {
        $error_message = "Error: Invalid booking or unauthorized action.";
    }
}

// Fetch updated user's bookings after cancellation
$userBookings = [];
try {
    $stmt = $pdo->prepare("
        SELECT b.booking_id, r.room_name, r.location, b.start_time, b.end_time, b.purpose 
        FROM Bookings b
        JOIN Rooms r ON b.room_id = r.room_id
        WHERE b.user_id = ?
        ORDER BY b.start_time
    ");
    $stmt->execute([$user_id]);
    $userBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}

date_default_timezone_set('Asia/Bahrain');
$current_date = new DateTime();

foreach ($userBookings as &$booking) {
    $booking_start_date = new DateTime($booking['start_time']);
    $booking_end_date = new DateTime($booking['end_time']);

    if ($current_date < $booking_start_date) {
        $booking['status'] = 'Upcoming';
    } elseif ($current_date > $booking_end_date) {
        $booking['status'] = 'Past';
    } else {
        $booking['status'] = 'Current';
    }
}
unset($booking); // Break the reference with the last element

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage your bookings</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/manage_booking.css">
    <link rel="stylesheet" href="../Css/footer.css">
</head>
<body>
<header>
    <nav>
        <img src="../css/Logo.png">
        <h1>IT College Booking System</h1>
        <ul>
            <li><a href="profile.php"><span class="material-symbols-outlined">person</span>Profile</a></li>
            <li><a href="index.php"><span class="material-symbols-outlined">house</span>Home Page</a></li>
            <li><a href="room_browse.php"><span class="material-symbols-outlined">room</span>Browse Rooms</a></li>
            <li><a href="logout.php"><span class="material-symbols-outlined">logout</span>Log Out</a></li>
        </ul>
    </nav>
</header>
<main>
    <h1>Your Booked Rooms</h1>

    <!-- Display success or error messages -->
    <?php if (!empty($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($userBookings)): ?>
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userBookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                        <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($booking['start_time']))); ?></td>
                        <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))); ?></td>
                        <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['end_time']))); ?></td>
                        <td><?php echo htmlspecialchars($booking['purpose']); ?></td>
                        <td class="status <?php echo strtolower($booking['status']); ?>"><?php echo htmlspecialchars($booking['status']); ?></td>
                        <td>
                            <?php if ($booking['status'] == 'Upcoming'): ?>
                                <form method="POST">
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
        <p class="no-bookings">You have no booked rooms yet. <a href="room_browse.php">Book a room now</a>.</p>
    <?php endif; ?>
</main>
<footer>
    <nav>
        <p>Contact us: <a href="mailto:booking.help@uob.edu.bh">booking.help@uob.edu.bh</a></p>
        <p>Follow us on social media: <a href="https://twitter.com/uobedubh">Twitter</a> <a href="https://www.instagram.com/uobedubh">Instagram</a></p>
        <p>&copy; 2024 University of Bahrain | All rights Reserved</p>
    </nav>
</footer>
</body>
</html>
