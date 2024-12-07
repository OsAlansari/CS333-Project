<?php 
// Start the session
session_start();

$isLoggedIn = isset($_SESSION['user_id']);

include 'config.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login-register.php');
    exit();
}

if ($user['user_type'] == 'Admin') {
    header('Location: index.php');
    exit();
}

// Fetch user's bookings
$userBookings = [];
if ($isLoggedIn) {
    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT(b.booking_id), r.room_name, r.location, b.start_time, b.end_time, b.purpose 
            FROM Bookings b
            JOIN Rooms r ON b.room_id = r.room_id
            WHERE b.user_id = ?
            GROUP BY b.booking_id, r.room_name, r.location, b.start_time, b.end_time, b.purpose
            ORDER BY b.start_time
        ");
        $stmt->execute([$user_id]);
        $userBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching bookings: " . $e->getMessage());
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
    <h1>IT collage booking system</h1>
            <ul>
                <li><a href="profile.php">
                    <span class="material-symbols-outlined">person</span>Profile
                </a></li>
                <li><a href="index.php">
                    <span class="material-symbols-outlined">house</span>Home Page
                </a></li>
                <li><a href="room_browse.php">
                    <span class="material-symbols-outlined">room</span>Bowse Rooms
                </a></li>
                <li><a href="logout.php">
                    <span class="material-symbols-outlined">logout</span>Log Out
                </a></li>
            </ul>
        </nav>
    </header>
    <main>
    <h1>Your Booked Rooms</h1>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userBookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($booking['end_time']))); ?></td>
                                <td><?php echo htmlspecialchars($booking['purpose']); ?></td>
                                <td>
                                    <form method="POST" action="cancel_booking.php">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit">Cancel</button>
                                    </form>
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
            <p>
            Contact us:
            <a href="mailto:booking.help@uob.edu.bh">booking.help@uob.edu.bh</a>
            </p>
            <p>
            Follow us on social media:
            <a href="https://twitter.com/uobedubh">Twitter</a>
            <a href="https://www.instagram.com/uobedubh">Instagram</a>
            </p>
            <p>&copy; 2024 University of Bahrain | All rights Reserved</p>
        </nav>
    </footer>
</body>
</html>