<?php
// Start the session
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

include 'config.php';

// Fetch user type
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$type = $stmt->fetch();
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
    <link rel="stylesheet" href="../Css/index.css">
    <link rel="stylesheet" href="../Css/footer.css">
</head>
<body>
    <!-- Navigation Bar -->
    <header>
    <nav>
    <img src="../css/Logo.png">
    <h1>IT collage booking system</h1>
            <ul>
                <?php if ($isLoggedIn): ?>
                    <li><a href="profile.php">
                        <span class="material-symbols-outlined">person</span>Profile
                    </a></li>
                    <li><a href="room_browse.php">
                        <span class="material-symbols-outlined">room</span>Bowse Rooms
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
                <?php else: ?>
                    <li><a href="login-register.php">
                        <span class="material-symbols-outlined">login</span>Sign Up
                    </a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
    <h1>Welcome to the Home Page</h1>
        <?php if ($isLoggedIn): ?>
            <p class="welcome">Welcome! Access your <a href="profile.php">profile</a>.</p>
        <?php else: ?>
            <p>Please log in or register to access more features.</p>
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
