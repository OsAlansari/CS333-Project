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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $profile_picture = $user['profile_picture']; // Retain current picture by default

    // Handle profile picture upload if a new file is uploaded
    if (!empty($_FILES['profile_picture']['name'])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["profile_picture"]["name"]);

        // Ensure uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            $profile_picture = $targetFile; // Save the new file path
        } else {
            echo "Error uploading file.";
        }
    }

    // Update email and profile picture
    $stmt = $pdo->prepare("UPDATE users SET email = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$email, $profile_picture, $user_id]);

    // Handle password change
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate current password
        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } else {
            // Hash the new password and update the database
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $user_id]);
            $success = "Password changed successfully!";
        }
    }

    // Redirect back to profile page if no error
    if (!isset($error)) {
        header('Location: profile.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/profile-edit.css">
</head>
<body>
<div class="edit-profile-container">
    <h1>Edit Profile</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture">
        </div>
        <h3>Change Password</h3>
        <?php if (isset($error)): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success-message"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password">
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>
        <button class="submit-button" type="submit">Update Profile</button>
    </form>
    <a href="profile.php" class="back-button">Back to Profile Page</a>
</div>
</body>
</html>
