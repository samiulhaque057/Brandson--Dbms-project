<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'temperature' => filter_input(INPUT_POST, 'temperature', FILTER_VALIDATE_FLOAT),
        'humidity' => filter_input(INPUT_POST, 'humidity', FILTER_VALIDATE_FLOAT),
        'used_capacity' => filter_input(INPUT_POST, 'used_capacity', FILTER_VALIDATE_FLOAT),
        'status' => $_POST['status'] ?? 'normal'
    ];
    
    $storageId = filter_input(INPUT_POST, 'storage_id', FILTER_VALIDATE_INT);

    if ($storageId && updateStorageConditions($storageId, $data)) {
        $_SESSION['success_message'] = "Storage conditions updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update storage conditions";
    }
}

header("Location: dashboard-template.php");
exit();