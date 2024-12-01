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

// If the user doesn't have a profile picture, assign a random one
if (empty($user['profile_picture'])) {
    $random_profile_image = 'https://avatar.iran.liara.run/public?' . rand(1, 1000);
    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $stmt->execute([$random_profile_image, $user_id]);
    $user['profile_picture'] = $random_profile_image;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/profile.css">
</head>
<body>
<div class="profile-container">
    <h1>Profile</h1>
    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
    <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
    <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <a href="profile.edit.php" class="button">Edit Profile</a>
    <a href="logout.php" class="button" style="background: linear-gradient(45deg, #ab6bff, #27014b);">Logout</a>
    <a href="index.php" class="button" style="background: linear-gradient(45deg, #ab6bff, #27014b);">back Home</a>
</div>
</body>
</html>
