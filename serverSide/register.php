<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);
            echo "Registration successful!";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { 
                echo "Username or email already exists.";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    } else {
        echo "All fields are required!";
    }
}
?>
