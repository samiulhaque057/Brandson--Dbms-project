<?php
include 'includes/config.php';

header('Content-Type: application/json');

$sensorData = [];

$query = "
    SELECT sr.*
    FROM sensor_readings sr
    INNER JOIN (
        SELECT storage_id, MAX(recorded_at) AS latest_time
        FROM sensor_readings
        GROUP BY storage_id
    ) latest ON sr.storage_id = latest.storage_id AND sr.recorded_at = latest.latest_time
";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $sensorData[$row['storage_id']] = [
        'temperature' => $row['temperature'],
        'humidity' => $row['humidity'],
        'battery_level' => $row['battery_level']
    ];
}

echo json_encode($sensorData);
?>
