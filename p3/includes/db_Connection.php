<?php
// db_connection.php

$host = 'localhost';
$dbname = 'brandson';
$username = 'root';
$password = ''; // If no password in XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "DB Connection Successful"; // (Optional: You can comment/remove this after testing)
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
