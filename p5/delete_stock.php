<?php
// delete_stock.php

include 'includes/config.php'; // Include the database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read the raw POST data as JSON
$input = file_get_contents("php://input"); // Read the raw input data

// Debug: Log the raw input data to see what you're getting
error_log("Raw Input: " . $input);

// Decode the JSON data
$data = json_decode($input, true);

// Debug: Log the decoded data to ensure JSON parsing works
error_log("Decoded Data: " . print_r($data, true));

// Check if 'batch' is passed
if (isset($data['batch'])) {
    $batch = $data['batch']; // Use the batch from the decoded JSON data

    // SQL DELETE query
    $sql = "DELETE FROM stockData WHERE batch = ?";

    // Prepare and execute the query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $batch); // Bind the batch parameter

        if ($stmt->execute()) {
            echo json_encode(['success' => true]); // Successful deletion response
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]); // Error response
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']); // Error preparing statement
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No batch specified']); // No batch specified in the request
}

$conn->close();
?>
