<?php
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
include 'config.php'; // Include database connection

// Check if the user is logged in
if (!$isLoggedIn) {
    header('Location: /login-register.php');
    exit();
}

// Fetch user type
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$type = $stmt->fetch();

$search = '';
if (isset($_GET['search'])) {
    try {
        $search = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM Rooms WHERE room_name LIKE ? ORDER BY location, room_name");
    $stmt->execute(['%' . $search . '%']);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Group rooms by location
        $grouped_rooms = [];
        foreach ($rooms as $room) {
            $grouped_rooms[$room['location']][] = $room;
        }
    
        // Move OpenLab to the top
        if (isset($grouped_rooms['OpenLab'])) {
            $openLabRooms = $grouped_rooms['OpenLab'];
            unset($grouped_rooms['OpenLab']);
            $grouped_rooms = ['OpenLab' => $openLabRooms] + $grouped_rooms;
        }
    } catch (PDOException $e) {
        die("Error fetching rooms: " . $e->getMessage());
    }
} else {
    try {
        // Fetch all rooms from the database
        $stmt = $pdo->prepare("SELECT * FROM Rooms ORDER BY location, room_name");
        $stmt->execute();
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Group rooms by location
        $grouped_rooms = [];
        foreach ($rooms as $room) {
            $grouped_rooms[$room['location']][] = $room;
        }
    
        // Move OpenLab to the top
        if (isset($grouped_rooms['OpenLab'])) {
            $openLabRooms = $grouped_rooms['OpenLab'];
            unset($grouped_rooms['OpenLab']);
            $grouped_rooms = ['OpenLab' => $openLabRooms] + $grouped_rooms;
        }
    } catch (PDOException $e) {
        die("Error fetching rooms: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Browse</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/room_browse.css">
    <link rel="stylesheet" href="../Css/footer.css">
</head>
<body>
<header>
    <nav>
    <img src="../css/Logo.png">
    <h1>IT collage booking system</h1>
    <form method="GET" action="room_browse.php">
            <input type="text" name="search" placeholder="Search by Room Name" value="<?= htmlspecialchars($search) ?>" required>
            <button type="submit">Search</button>
        </form>
            <ul>
                    <li><a href="profile.php">
                        <span class="material-symbols-outlined">person</span>Profile
                    </a></li>
                    <li><a href="index.php">
                        <span class="material-symbols-outlined">house</span>Home Page
                    </a></li>
                    <?php if ($type['user_type'] == 'Admin'): ?>
                        <li><a href="admin.php">
                            <span class="material-symbols-outlined">admin_panel_settings</span>Admin Panel
                        </a></li>
                        <?php else: ?>
                        <li><a href="manage_booking.php">
                            <span class="material-symbols-outlined">table</span>Manage Your Bookings
                        </a></li>
                        <?php endif; ?>
                    <li><a href="logout.php">
                        <span class="material-symbols-outlined">logout</span>Log Out
                    </a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Browse Rooms</h1>
        <div class="room-container">
        <?php foreach ($grouped_rooms as $location => $rooms): ?>
            <div class="location-section">
                <h2><?php echo htmlspecialchars($location); ?> Rooms</h2>
                <div class="room-grid">
                    <?php foreach ($rooms as $room): ?>
                        <div class="room-card">
                            <h3><?php echo htmlspecialchars($room['room_name']); ?></h3>
                            <p>Type: <?php echo htmlspecialchars($room['room_type']); ?></p>
                            <p>Location: <?php echo htmlspecialchars($room['location']); ?></p>
                            <!-- Add the View Details link here -->
                            <a href="room_details.php?id=<?php echo urlencode($room['room_id']); ?>">View Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
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