<?php
include 'includes/config.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'location' => $_POST['location'],
        'total_capacity' => $_POST['total_capacity'],
        'used_capacity' => $_POST['used_capacity'],
        'current_temp' => $_POST['current_temp'],
        'humidity' => $_POST['humidity'],
        'status' => $_POST['status'],
        'product_status' => json_encode($_POST['products'])
    ];

    $stmt = $pdo->prepare("INSERT INTO cold_storages 
        (location, total_capacity, used_capacity, current_temp, humidity, status, product_status)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute(array_values($data))) {
        $_SESSION['success_message'] = "Storage added successfully!";
        header("Location: dashboard.php");
        exit();
    }
}

// HTML form would go here with input fields matching the database columns