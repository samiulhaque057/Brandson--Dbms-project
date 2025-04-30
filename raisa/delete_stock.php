<?php
// delete_stock.php

include 'includes/config.php';
include 'includes/dbConnection.php'; // Include db connection
include 'includes/functions.php';
session_start(); // Start session for messages

header('Content-Type: application/json'); // Set header to indicate JSON response

// Read the raw POST data as JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$response = array('success' => false, 'error' => '');

// Check if 'batch' is passed in the JSON data
if (isset($data['batch'])) {
    $batch = sanitize($data['batch']); // Sanitize the batch number

    // SQL DELETE query
    $sql = "DELETE FROM stockData WHERE batch = ?";

    // Prepare and execute the query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $batch); // Bind the batch parameter as string 's'

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                // Optionally set a success message in session if needed for a redirect
                // $_SESSION['success_message'] = "Stock with batch " . htmlspecialchars($batch) . " deleted successfully.";
            } else {
                $response['error'] = "No stock found with batch number: " . htmlspecialchars($batch) . " or already deleted.";
                 // Optionally set an error message in session
                 // $_SESSION['error_message'] = "No stock found with batch number: " . htmlspecialchars($batch) . " or already deleted.";
            }
        } else {
            $response['error'] = "Error deleting stock: " . $stmt->error;
             // Optionally set an error message in session
             // $_SESSION['error_message'] = "Error deleting stock: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['error'] = "Database error preparing statement: " . $conn->error;
         // Optionally set an error message in session
         // $_SESSION['error_message'] = "Database error: " . $conn->error;
    }

    // Close connection (optional, depending on your includes/dbConnection.php)
    // $conn->close();

} else {
    $response['error'] = "Invalid request: Batch number not provided.";
     // Optionally set an error message in session
     // $_SESSION['error_message'] = "Invalid request: Batch number not provided.";
}

echo json_encode($response); // Return JSON response
?>