<?php
include 'includes/config.php'; // Your DB connection

if (isset($_POST['update_inventory'])) {
    $id = $_POST['id'];
    $product = $_POST['product'];
    $batch = $_POST['batch'];
    $quantity = $_POST['quantity'];
    $expires = $_POST['expires'];
    $storage = $_POST['storage'];
    $status = $_POST['status'];

    // Update the database
    $sql = "UPDATE expiring_inventory 
            SET 
                product = ?, 
                batch = ?, 
                quantity = ?, 
                expires = ?, 
                storage = ?, 
                status = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssi", $product, $batch, $quantity, $expires, $storage, $status, $id);

    if ($stmt->execute()) {
        // Success! Redirect back to dashboard
        header("Location: dashboard-template.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}
?>
