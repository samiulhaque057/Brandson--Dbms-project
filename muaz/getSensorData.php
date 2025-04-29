<?php

require_once 'includes/config.php'; // Ensure this includes your DB connection
require_once 'includes/functions.php';

function getLatestSensorData() {
    // Your database connection is in config.php
    global $conn; // Make sure the connection is global if you're using it here
    
    // SQL query to get the latest sensor data per storage unit
    $sql = "
        SELECT sr.storage_id, sr.temperature, sr.humidity, sr.sensor_status, sr.battery_level, sr.recorded_at
        FROM sensor_readings sr
        INNER JOIN (
            SELECT storage_id, MAX(recorded_at) AS latest
            FROM sensor_readings
            GROUP BY storage_id
        ) latest_sr
        ON sr.storage_id = latest_sr.storage_id AND sr.recorded_at = latest_sr.latest
    ";

    // Execute the query
    $result = $conn->query($sql);
    $sensorData = [];

    // Check if the query was successful
    if ($result && $result->num_rows > 0) {
        // Fetch all rows from the result set
        while ($row = $result->fetch_assoc()) {
            // Store each storage unit's sensor data in the array
            $sensorData[$row['storage_id']] = [
                'temperature' => $row['temperature'],
                'humidity' => $row['humidity'],
                'sensor_status' => $row['sensor_status'],
                'battery_level' => $row['battery_level'],
                'recorded_at' => $row['recorded_at']
            ];
        }
    } else {
        // No data found, you can handle it or return an empty array
        $sensorData = ['error' => 'No data found for any storage units'];
    }

    return $sensorData;
}
?>
