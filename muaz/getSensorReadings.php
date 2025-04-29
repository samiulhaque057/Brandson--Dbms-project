<?php
// Database connection parameters
$host = 'localhost';         // Change this to your database host
$db = 'brandson';  // Your database name
$user = 'root';     // Your database username
$pass = '';     // Your database password

// Create a new PDO instance to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to get the most recent sensor readings (temperature, humidity, recorded_at)
    $stmt = $pdo->prepare("SELECT recorded_at, temperature, humidity FROM sensor_readings ORDER BY recorded_at DESC LIMIT 10");
    $stmt->execute();

    // Fetch all results as an associative array
    $sensorReadings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert data to JSON format for use in JavaScript
    echo json_encode($sensorReadings);

} catch (PDOException $e) {
    // Handle any errors with a friendly message
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>
