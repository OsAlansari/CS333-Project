<?php
// Start the session
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

include 'config.php';

// Fetch user's bookings
$userBookings = [];
if ($isLoggedIn) {
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <!-- Additional CSS -->
    <link rel="stylesheet" href="../Css/index.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <li><a href="profile.php">
                    <span class="material-symbols-outlined">person</span>Profile
                </a></li>
                <li><a href="room_browse.php">
                    <span class=""></span>Bowse Rooms
                </a></li>
                <li><a href="room_details.php">
                </a></li>
                <li><a href="logout.php">
                    <span class="material-symbols-outlined">logout</span>Log Out
                </a></li>
            <?php else: ?>
                <li><a href="login-register.php">
                    <span class="material-symbols-outlined">login</span>Sign Up
                </a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <main>
    <h1>Welcome to the Home Page</h1>
        <?php if ($isLoggedIn): ?>
            <p class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?>! Access your <a href="profile.php">profile</a>.</p>

            <h2>Your Booked Rooms</h2>
            <?php if (!empty($userBookings)): ?>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Room Name</th>
                            <th>Location</th>
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
                                <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($booking['start_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($booking['end_time']))); ?></td>
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
        <?php else: ?>
            <p>Please log in or register to access more features.</p>
        <?php endif; ?>
    </main>
</body>
</html>
