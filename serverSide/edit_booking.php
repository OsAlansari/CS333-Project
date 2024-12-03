<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Fetch booking and room details
$id = intval($_GET['id']);
$booking = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        die("Booking not found.");
    }

    // Fetch rooms for the dropdown
    $roomStmt = $pdo->prepare("SELECT id, name FROM rooms");
    $roomStmt->execute();
    $rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1>Edit Booking</h1>
        <form action="update_booking.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($booking['id']) ?>">
            <div class="mb-3">
                <label for="room_id" class="form-label">Room</label>
                <select id="room_id" name="room_id" class="form-select" required>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= htmlspecialchars($room['id']) ?>" <?= $room['id'] == $booking['room_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($room['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" id="start_time" name="start_time" class="form-control" value="<?= htmlspecialchars($booking['start_time']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" id="end_time" name="end_time" class="form-control" value="<?= htmlspecialchars($booking['end_time']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="admin_schedule.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
