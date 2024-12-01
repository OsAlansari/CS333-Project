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
    <link rel="stylesheet" href="../Css/index.css"> <!-- Ensure the CSS path is correct -->
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Log Out</a></li>
            <?php else: ?>
                
                <li><a href="login-register.php">Sign up</a></li>
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
