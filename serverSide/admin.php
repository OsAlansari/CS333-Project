<?php
// Start the session
session_start();

include 'config.php';

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
