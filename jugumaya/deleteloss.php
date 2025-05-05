<?php
include 'includes/config.php';
include 'includes/dbConnection.php'; // Include db connection directly
include 'includes/functions.php';
session_start(); // Start session for messages

// Check if ID is provided in the URL and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = sanitize($_GET['id']); // Sanitize the ID

    // Prepare the SQL query to delete from the loststock table
    $sql = "DELETE FROM loststock WHERE loss_id = ?";

    // Use prepared statement for security
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id); // 'i' indicates integer type

        // Execute the statement
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['success_message'] = "Loss record deleted successfully.";
            } else {
                 // No row was deleted (ID not found or already deleted)
                 $_SESSION['error_message'] = "No loss record found with ID: " . htmlspecialchars($id) . " or it was already deleted.";
            }
             // Redirect back to the dashboard after deletion attempt
            header("Location: dashboard-1.php");
            exit();
        } else {
            // Error executing the delete statement
            $_SESSION['error_message'] = "Error deleting record: " . $stmt->error;
             // Redirect back to the dashboard on error
            header("Location: dashboard-1.php");
            exit();
        }

        // Close statement
        $stmt->close();
    } else {
         // Error preparing the delete statement
         $_SESSION['error_message'] = "Database error preparing delete statement: " . $conn->error;
         // Redirect back to the dashboard on error
         header("Location: dashboard-1.php");
         exit();
    }

    // Close connection
    $conn->close();
} else {
    // No valid ID provided in the URL
    $_SESSION['error_message'] = "Invalid or missing loss record ID specified for deletion.";
     // Redirect back to the dashboard
    header("Location: dashboard-1.php");
    exit();
}
?>