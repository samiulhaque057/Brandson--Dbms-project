<?php


include "dbConnection.php";

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $userType = $_POST['user_type'];
    $password = $_POST['password'];
    $repeatPassword = $_POST['repeat_password'];

    // Check if passwords match
    if ($password != $repeatPassword) {
        header("Location: register.html?error=" . urlencode("Passwords do not match."));
        exit();
    }

    // Check if email already exists
    $checkEmailSql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: register.html?error=" . urlencode("Email is already registered."));
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert data into the 'user' table
    $insertSql = "INSERT INTO user (first_name, last_name, email, user_type, password) 
                  VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $userType, $hashedPassword);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: login.html?success=" . urlencode("Registration successful!"));
        exit();
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: register.html?error=" . urlencode("Registration failed: " . $error));
        exit();
    }
}
?>
