<?php
// delete_stock.php

// Includes (DO NOT CHANGE per user instruction)
include 'includes/config.php';
include 'includes/dbConnection.php'; // Include database connection
include 'includes/functions.php'; // Include functions

// Start session for messages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the request method is POST (from the form submission on the dashboard)
// We check for $conn here because we cannot change dbConnection.php to handle connection errors gracefully
if ($_SERVER["REQUEST_METHOD"] == "POST" && $conn) {
    // Get the item ID from the POST data
    // Filter to ensure it's a valid integer
    $itemId = filter_var($_POST['item_id'] ?? null, FILTER_VALIDATE_INT);

    // Validate the item ID
    if ($itemId === false || $itemId <= 0) {
         $_SESSION['error_message'] = "Invalid item ID provided for deletion.";
    } else {
        // Valid ID, proceed with deletion from the 'inventory' table

        // Prepare the SQL DELETE statement using a prepared statement
        $sql = "DELETE FROM inventory WHERE id = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind the item ID parameter
            $stmt->bind_param("i", $itemId); // 'i' indicates an integer parameter

            // Execute the prepared statement
            if ($stmt->execute()) {
                 // Check if any rows were affected (i.e., if an item was actually deleted)
                 if ($stmt->affected_rows > 0) {
                    $_SESSION['success_message'] = "Inventory item deleted successfully.";
                 } else {
                    // No rows affected might mean the ID didn't exist
                    $_SESSION['error_message'] = "Inventory item not found or already deleted.";
                 }
            } else {
                // If execution failed, set an error message and log the error
                $_SESSION['error_message'] = "Error deleting inventory item: " . $stmt->error;
                error_log("MySQL Execute Error (delete_stock.php): " . $stmt->error); // Log the specific SQL error
            }

            // Close the prepared statement
            $stmt->close();

        } else {
             // If statement preparation failed
             $_SESSION['error_message'] = "Database error preparing delete statement: " . $conn->error;
             error_log("MySQL Prepare Error (delete_stock.php): " . $conn->error); // Log the specific SQL error
        }
    } // End of validation checks

} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !$conn) {
    // Handle case where form was submitted but DB connection failed
     $_SESSION['error_message'] = "Database connection failed. Cannot delete item.";
} else {
    // If the script was accessed directly or with a method other than POST
    $_SESSION['error_message'] = "Invalid request method.";
}

// Redirect the user back to the dashboard page after processing
// This is crucial after a POST request like deletion
header("Location: productseller.php");
exit(); // Stop script execution after redirection

// Close the database connection (optional, PHP does this automatically at the end of script execution)
// if ($conn) { $conn->close(); }
?>
