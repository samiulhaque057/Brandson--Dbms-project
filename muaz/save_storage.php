<?php
// Start output buffering
ob_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_Connection.php';

if (isset($_POST['add_storage'])) {
    $location = $_POST['location'];
    $total_capacity = $_POST['total_capacity'];
    $used_capacity = $_POST['used_capacity'];
    $status = $_POST['status'];
    $sensor_id = $_POST['sensor_id'];
    $sensor_name = $_POST['sensor_name'];
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // First insert into sensor table
        $stmt = $conn->prepare("INSERT INTO sensor (sensor_id, sensor_name, temperature, humidity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isdd", $sensor_id, $sensor_name, $temperature, $humidity);
        $stmt->execute();
        $stmt->close();
        
        // Then insert into cold_storages table with the sensor_id
        $stmt = $conn->prepare("INSERT INTO cold_storages (location, total_capacity, used_capacity, status, sensor_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sddsi", $location, $total_capacity, $used_capacity, $status, $sensor_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Cold storage added successfully with sensor!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['success_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: dashboard-template.php");
    exit();
}

// End output buffering and flush
ob_end_flush();
?>