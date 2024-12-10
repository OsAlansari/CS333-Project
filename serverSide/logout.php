<?php
session_start();
session_destroy();
header('Location: ../serverSide/index.php'); // Redirect to the home page
exit();
?>
