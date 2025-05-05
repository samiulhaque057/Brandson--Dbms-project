<?php
include 'dbConnection.php'; // include your DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize inputs
    $item = $_POST['item'];
    $product_type = $_POST['product_type'];
    $quantity_change = $_POST['quantity_change'];
    $reason = $_POST['reason'];
    $adjustment_date = $_POST['adjustment_date'];
    $adjustment_time = $_POST['adjustment_time'];
    $adjustment_type = $_POST['adjustment_type'];
    $notes = isset($_POST['notes']) ? $_POST['notes'] : null;

    // Prepare and bind statement
    $stmt = $conn->prepare("INSERT INTO stock_adjustments 
        (item, product_type, quantity_change, reason, adjustment_date, adjustment_time, adjustment_type, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssdsssss", $item, $product_type, $quantity_change, $reason, $adjustment_date, $adjustment_time, $adjustment_type, $notes);

    if ($stmt->execute()) {
        echo "Stock adjustment recorded successfully.";
        // Or redirect if needed
        // header("Location: success.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
