<?php
// Start session
session_start();

//  the database connection
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the log user's ID
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // get  user data 
    $stmt = $conn->prepare("SELECT * FROM Users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handleing  profile updates
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Validate email format
    if (!preg_match("/^[0-9]{8}@stu\.uob\.edu\.bh$/", $email)) {
        echo "Invalid email format. Please use your UoB email (e.g., 201928028@stu.uob.edu.bh).";
        exit();
    }

    // Handle profile picture upload
    $profile_picture = $user['profile_picture'] ?? null; 
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $unique_file_name = uniqid() . "-" . basename($_FILES['profile_picture']['name']);
        $target_file = $target_dir . $unique_file_name;

        // Validate file type (only allow images)
        $allowed_types = ['image/jpeg', 'image/png'];
        if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $profile_picture = $target_file;
            } else {
                echo "Failed to upload profile picture.";
                exit();
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, are aloowed";
            exit();
        }
    }

    // Update user profile (excluding password for now)
    $stmt = $conn->prepare("UPDATE Users SET username = :username, email = :email, profile_picture = :profile_picture WHERE id = :id");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':profile_picture' => $profile_picture,
        ':id' => $user_id
    ]);

    // Handle password update if submitted
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        // Verify the current password
        if (password_verify($current_password, $user['password'])) {
            // Hash the new password and update it
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE Users SET password = :password WHERE id = :id");
            $stmt->execute([
                ':password' => $hashed_password,
                ':id' => $user_id
            ]);
            echo "Password updated successfully!";
        } else {
            echo "Current password is incorrect.";
        }
    }

    echo "Profile updated successfully!";
}
?>
