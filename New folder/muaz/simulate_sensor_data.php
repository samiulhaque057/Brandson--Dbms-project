<?php
// simulate_sensor_data.php
include 'includes/config.php';
require_once 'includes/functions.php';



function insertRandomSensorData($storageId) {
    global $conn;

    // Generate random temperature between 2°C and 18°C
    $temperature = mt_rand(20, 180) / 10;

    // Generate random humidity between 50% and 90%
    $humidity = mt_rand(500, 900) / 10;

    // Generate random battery level between 60% and 100%
    $battery = mt_rand(60, 100);

    // Randomly set sensor status
    $status = (mt_rand(0, 10) > 1) ? 'active' : 'inactive'; // 90% active, 10% inactive

    $sql = "INSERT INTO sensor_readings (storage_id, temperature, humidity, battery_level, sensor_status) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iddis", $storageId, $temperature, $humidity, $battery, $status);

    if ($stmt->execute()) {
        echo "Inserted new reading for Storage ID $storageId: Temp={$temperature}°C, Humidity={$humidity}%, Battery={$battery}%<br>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Get all cold_storage IDs
$storageQuery = $conn->query("SELECT id FROM cold_storage");
if ($storageQuery->num_rows > 0) {
    while ($row = $storageQuery->fetch_assoc()) {
        insertRandomSensorData($row['id']);
    }
} else {
    echo "No cold storages found!";
}

$conn->close();
?>
