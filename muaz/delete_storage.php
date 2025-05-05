<?php
// Start output buffering
ob_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_Connection.php';

$storage_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($storage_id > 0) {
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get the sensor_id associated with this cold storage
        $stmt = $conn->prepare("SELECT sensor_id FROM cold_storages WHERE coldstorage_id = ?");
        $stmt->bind_param("i", $storage_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $sensor_id = $row['sensor_id'];
        $stmt->close();
        
        // Delete the cold storage record
        $stmt = $conn->prepare("DELETE FROM cold_storages WHERE coldstorage_id = ?");
        $stmt->bind_param("i", $storage_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete the associated sensor
        if ($sensor_id) {
            $stmt = $conn->prepare("DELETE FROM sensor WHERE sensor_id = ?");
            $stmt->bind_param("i", $sensor_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Cold storage and associated sensor deleted successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['success_message'] = "Error: " . $e->getMessage();
    }
}

header("Location: dashboard-template.php");
exit();

// End output buffering and flush
ob_end_flush();
?>