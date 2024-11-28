<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
</head>
<body>
    <h1>Your Profile</h1>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p>
        <strong>Profile Picture:</strong><br>
        <img src="<?= htmlspecialchars($user['profile_picture'] ?: 'default.jpg') ?>" alt="Profile Picture" width="150">
    </p>

    <a href="profile.edit.php">Edit Profile</a>
    <a href="logout.php">Logout</a>
</body>
</html>
