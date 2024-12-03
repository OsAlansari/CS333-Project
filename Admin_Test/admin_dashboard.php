<?php
// Start session and check if the user is an admin
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Include config file for database connection (if needed for dashboard stats)
include 'config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_room_management.php">Room Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_schedule.php">Schedule Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center">Welcome to the Admin Dashboard</h1>
        <div class="row text-center mt-4">
            <div class="col-md-6">
                <a href="admin_room_management.php" class="btn btn-primary btn-lg w-100">Manage Rooms</a>
            </div>
            <div class="col-md-6">
                <a href="admin_schedule.php" class="btn btn-secondary btn-lg w-100">Manage Schedules</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript for responsiveness -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
