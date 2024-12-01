<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Login logic
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php'); // Redirect to home page after login
            exit();
        } else {
            $login_error = 'Invalid email or password.';
        }
    } elseif (isset($_POST['register'])) {
        // Register logic
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!preg_match('/^[0-9]{8}@stu\.uob\.edu\.bh$/', $email)) {
            $register_error = 'Invalid UoB email format.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $register_error = 'This email is already registered.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $hashedPassword]);

                // Set session and redirect to home page after successful registration
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $newUser = $stmt->fetch();
                $_SESSION['user_id'] = $newUser['id'];

                header('Location: index.php'); // Redirect to home page
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <title>Login/Register</title>
</head>
<body>
<div class="main">
    <input type="checkbox" id="chk" aria-hidden="true">

    <div class="signup">
        <form method="POST" action="">
            <label for="chk" aria-hidden="true">Sign up</label>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="UoB Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Sign up</button>
        </form>
    </div>

    <div class="login">
        <form method="POST" action="">
            <label for="chk" aria-hidden="true">Login</label>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</div>
</body>
</html>
