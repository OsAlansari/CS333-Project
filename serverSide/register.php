<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate UoB email
    if (!preg_match('/^[0-9]{8}@stu\.uob\.edu\.bh$/', $email)) {
        die('Invalid UoB email format.');
    }

    // Check if the email is already registered
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo 'This email is already registered. <a href="login.php">Login here</a>.';
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);

    echo 'Registration successful! <a href="login.php">Click here to login.</a>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form method="POST" action="">

        <label for="username">username:</label>
        <input type="username" id="username" name="username" required>
        
        <label for="email">UoB Email:</label>
        <input type="email" id="email" name="email" pattern="[0-9]{8}@stu\.uob\.edu\.bh" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>