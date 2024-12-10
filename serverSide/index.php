<?php
// Start the session
session_start();
include 'config.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch user type
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$type = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

<!-- fonts style -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">



<!-- responsive style -->
<link href="../Css/responsive.css" rel="stylesheet" />
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <!-- Additional CSS -->
    
    <link rel="stylesheet" href="../Css/index.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div class="hero_area">

<div class="hero_bg_box">
  <div class="bg_img_box">
    <img src="../images/hero-bg.png" alt="">
  </div>
</div>

<!-- header section strats -->
<header>
    <nav>
        
    <img src="../images/Logo.png">
    <h1>IT collage booking system</h1>
            <ul>
                <?php if ($isLoggedIn): ?>
                    <li><a href="profile.php">
                        <span class="material-symbols-outlined">person</span>Profile
                    </a></li>
                    <li><a href="room_browse.php">
                        <span class="material-symbols-outlined">room</span>Bowse Rooms
                    </a></li>
                    <?php if ($type['user_type'] == 'Admin'): ?>
                        <li><a href="admin.php">
                            <span class="material-symbols-outlined">admin_panel_settings</span>Admin Panel
                        </a></li>
                        <?php else: ?>
                        <li><a href="manage_booking.php">
                            <span class="material-symbols-outlined">table</span>Manage Your Bookings
                        </a></li>
                        <?php endif; ?>
                    <li><a href="logout.php">
                        <span class="material-symbols-outlined">logout</span>Log Out
                    </a></li>
                <?php else: ?>
                    <li><a href="login-register.php">
                        <span class="material-symbols-outlined">login</span>Sign Up
                    </a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <section class="slider_section ">
      <div id="customCarousel1" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="container ">
              <div class="row">
                <div class="col-md-6 ">
                  <div class="detail-box">
                    <h1>
                      University of Bahrain <br>
                      Room Booking System
                    </h1>
                    <p>
                    Spaces to Inspire, Rooms to Achieve – Book Your Perfect Study Haven Today.
                    </p>
                    <div class="btn-box">
                      <a href="../serverSide\room_browse.php" class="btn1">
                        Book Now
                      </a>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="img-box">
                    <img src="../images/slider-img.png" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
    </section>
    
  </div>
</body>
</html>
