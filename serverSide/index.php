<?php
// Start the session
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
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
    <style>
        .material-symbols-outlined {
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
            vertical-align: middle; /* Aligns the icon with text */
            margin-right: 5px;     /* Adds spacing between icon and text */
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <li><a href="profile.php">
                    <span class="material-symbols-outlined">person</span>Profile
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
            <p>You are logged in. Access your <a href="profile.php">profile</a>.</p>
        <?php else: ?>
            <p>Please log in or register to access more features.</p>
        <?php endif; ?>
    </main>
</body>
</html>
