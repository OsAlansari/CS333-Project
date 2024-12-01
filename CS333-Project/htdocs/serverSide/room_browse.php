<?php
include 'config.php';

$filter_location = isset($_GET['location']) ? $_GET['location'] : '';
$filter_capacity = isset($_GET['capacity']) ? $_GET['capacity'] : '';
$filter_equipment = isset($_GET['equipment']) ? $_GET['equipment'] : '';

// Build SQL query dynamically based on filters
$query = "SELECT * FROM rooms WHERE 1";
$params = [];

if ($filter_location) {
    $query .= " AND location LIKE ?";
    $params[] = "%$filter_location%";
}

if ($filter_capacity) {
    $query .= " AND capacity >= ?";
    $params[] = $filter_capacity;
}

if ($filter_equipment) {
    $query .= " AND equipment LIKE ?";
    $params[] = "%$filter_equipment%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rooms = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Browsing</title>
</head>
<body>
    <h1>Browse Rooms</h1>

    <form method="GET">
        <label for="location">Location:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($filter_location) ?>">

        <label for="capacity">Min Capacity:</label>
        <input type="number" name="capacity" value="<?= htmlspecialchars($filter_capacity) ?>">

        <label for="equipment">Equipment:</label>
        <input type="text" name="equipment" value="<?= htmlspecialchars($filter_equipment) ?>">

        <button type="submit">Apply Filters</button>
    </form>

    <h2>Available Rooms</h2>
    <ul>
        <?php foreach ($rooms as $room): ?>
            <li>
                <h3><?= htmlspecialchars($room['name']) ?></h3>
                <p>Location: <?= htmlspecialchars($room['location']) ?></p>
                <p>Capacity: <?= htmlspecialchars($room['capacity']) ?> people</p>
                <p>Equipment: <?= htmlspecialchars($room['equipment']) ?></p>
                <a href="room_details.php?id=<?= $room['id'] ?>">View Details</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
