<?php
// Start session to store user data after login
session_start();

// Include database configuration file
include 'dbConnection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values from the form
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Prepare and bind the SQL query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND user_type = ?");
    $stmt->bind_param("ss", $email, $user_type);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching user is found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password 
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];

            // Redirect to the dashboard with a success message
            header("Location: dashboard.html?success=" . urlencode("Login successful!"));
            exit();
        } else {
            // Invalid password error message
            $error_message = "Invalid password!";
            header("Location: login.html?error=" . urlencode($error_message));
            exit();
        }
    } else {
        // No user found error message
        $error_message = "No user found with this email and user type.";
        header("Location: login.html?error=" . urlencode($error_message));
        exit();
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>
