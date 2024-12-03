<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Database connection

$id = intval($_GET['id']);
$room = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$room) {
        die("Room not found.");
    }
} catch (Exception $e) {
    die("Error fetching room: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1>Edit Room</h1>
        <form action="update_room.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($room['id']) ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Room Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($room['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" id="capacity" name="capacity" class="form-control" value="<?= htmlspecialchars($room['capacity']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="equipment" class="form-label">Equipment</label>
                <input type="text" id="equipment" name="equipment" class="form-control" value="<?= htmlspecialchars($room['equipment']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="admin_room_management.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
