<?php
// Start the session
session_start();
include 'config.php';

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
    <link rel="stylesheet" href="../Css/header.css">
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="../Css/footer.css">
</head>
<body>
    <!-- Navigation Bar -->
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

    <!-- Main Content -->
    <main>
    <h1>Admin Panel</h1>
    <div class="admin-container">
        <!-- Rooms Control Section -->
        <div class="section">
            <h2>Rooms Control</h2>
            <div class="grid">
                <div class="card">
                    <h3>Add Room</h3>
                    <p>Add a new room to the system</p>
                    <a href="../serverSide/add_room.php">Click here!</a>
                </div>
                <div class="card">
                    <h3>Edit Room</h3>
                    <p>Edit an existing room in the system</p>
                    <a href="../serverSide/edit_room.php">Click here!</a>
                </div>
                <div class="card">
                    <h3>Delete Room</h3>
                    <p>Delete an existing room from the system</p>
                    <a href="../serverSide/delete_room.php">Click here!</a>
                </div>
            </div>
        </div>

        <!-- Bookings Control Section -->
        <div class="section">
            <h2>Bookings Control</h2>
            <div class="grid">
                <div class="card">
                    <h3>Add Booking</h3>
                    <p>Add a new booking to the system</p>
                    <a href="../serverSide/add_booking.php">Click here!</a>
                </div>
                <div class="card">
                    <h3>Edit Booking</h3>
                    <p>Edit an existing booking in the system</p>
                    <a href="../serverSide/edit_booking.php">Click here!</a>
                </div>
                <div class="card">
                    <h3>Delete Booking</h3>
                    <p>Delete an existing booking from the system</p>
                    <a href="../serverSide/delete_booking.php">Click here!</a>
                </div>
            </div>
        </div>
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
