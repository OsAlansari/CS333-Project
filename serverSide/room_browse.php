<?php
session_start();
include 'config.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login-register.php');
    exit();
}

$user_id = $_SESSION['user_id'];

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
} catch (PDOException $e) {
    die("Error fetching rooms: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Browse</title>
    <link rel="stylesheet" href="../Css/room_browse.css">
</head>
<body>
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
</body>
</html>