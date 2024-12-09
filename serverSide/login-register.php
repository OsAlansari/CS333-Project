<?php
include 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Login logic
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (strpos($email, ' ') !== false || strpos($password, ' ') !== false) {
            $login_error = 'Email and password cannot contain spaces.';
        } else {
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
        }
    } elseif (isset($_POST['register'])) {
        // Register logic
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        if (strpos($first_name, ' ') !== false) {
            $register_error = 'First name cannot contain spaces.';
        } elseif (strpos($last_name, ' ') !== false) {
            $register_error = 'Last name cannot contain spaces.';
        } elseif (strpos($email, ' ') !== false || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_error = 'Invalid email format or contains spaces.';
        } elseif (strpos($password, ' ') !== false) {
            $register_error = 'Password cannot contain spaces.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/', $password)) { 
            $register_error = "Choose a stronger password.";
        } elseif (!preg_match('/^[0-9]{8,9}@stu\.uob\.edu\.bh$/', $email) && !preg_match('/^[a-zA-Z]{4,}@uob\.edu\.bh$/', $email)) {
            $register_error = 'Invalid UoB email format.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $register_error = 'This email is already registered.';
            } else {
                $user_type = 'student';
                if (preg_match('/^[a-zA-Z]{4,}@uob\.edu\.bh$/', $email)) {
                    $user_type = 'staff';
                }
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $hashedPassword, $user_type]);

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
    <link rel="stylesheet" href="../Css/login-register.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <title>Login/Register</title>
    <script>
        function validateForm() {
            const inputs = ["firstName", "lastName", "email", "password"];
            let valid = true;

            inputs.forEach((inputId) => {
                const input = document.getElementById(inputId);
                const error = document.getElementById(`${inputId}Error`);

                error.style.display = "none";

                if (input.value.includes(" ")) {
                    error.style.display = "block";
                    error.innerText = `${inputId.replace(/^./, inputId[0].toUpperCase())} cannot contain spaces.`;
                    valid = false;
                }
            });

            return valid; // Allow form submission if no errors
        }
    </script>
</head>
<body>
<div class="main">
    <input type="checkbox" id="chk" aria-hidden="true">

    <div class="signup">
        <form method="POST" action="" onsubmit="return validateForm()">
            <label for="chk" aria-hidden="true">Sign up</label>
            <input type="text" id="firstName" name="first_name" placeholder="First Name" required>
            <span id="firstNameError" class="error" style="display:none; color:red;"></span>

            <input type="text" id="lastName" name="last_name" placeholder="Last Name" required>
            <span id="lastNameError" class="error" style="display:none; color:red;"></span>

            <input type="email" id="email" name="email" placeholder="UoB Email" required>
            <span id="emailError"class="error" style="display:none; color:red;"></span>

            <input type="password" id="password" name="password" placeholder="Password" required>
            <span id="passwordError"class="error" style="display:none; color:red;"></span>

            <button type="submit" name="register">Sign up</button>
            <?php if (isset($register_error)): ?>
                <p class="login-error"><?php echo htmlspecialchars($register_error); ?></p>
            <?php endif; ?>
        </form>
    </div>

    <div class="login">
        <form method="POST" action="" onsubmit="return validateForm()">
            <label for="chk" aria-hidden="true">Login</label>
            <input type="text" id="email" name="email" placeholder="Email" required>
            <span id="emailError" class="error" style="display:none; color:red;"></span>

            <input type="password" id="password" name="password" placeholder="Password" required>
            <span id="passwordError"class="error" style="display:none; color:red;"></span>

            <button type="submit" name="login">Login</button>
            <?php if (isset($login_error)): ?>
                <p class="login-error"><?php echo htmlspecialchars($login_error); ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>
</body>
</html>
