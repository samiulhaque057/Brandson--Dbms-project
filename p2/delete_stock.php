<?php
include 'includes/config.php'; // Include your database connection
include 'includes/functions.php'; // Include any helper functions
session_start(); // Start session for messages

// Check if ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and bind
    $sql = "DELETE FROM meat_losses WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);

        // Execute the statement
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['success_message'] = "Loss record deleted successfully.";
            } else {
                 $_SESSION['error_message'] = "No loss record found with ID: " . htmlspecialchars($id) . " or already deleted.";
            }
            header("Location: dashboard.php"); // Redirect back to dashboard
            exit();
        } else {
            $_SESSION['error_message'] = "Error deleting record: " . $stmt->error;
            header("Location: dashboard.php"); // Redirect back to dashboard
            exit();
        }

        // Close statement
        $stmt->close();
    } else {
         $_SESSION['error_message'] = "Database error: " . $conn->error;
         header("Location: dashboard.php"); // Redirect back to dashboard
         exit();
    }

    // Close connection
    $conn->close();
} else {
    // No ID provided
    $_SESSION['error_message'] = "No loss record ID specified for deletion.";
    header("Location: dashboard.php");
    exit();
}
?>