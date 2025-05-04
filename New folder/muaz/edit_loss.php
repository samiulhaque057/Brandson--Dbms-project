<?php
include 'includes/config.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $product = $_POST['product'];
    $batch = $_POST['batch'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    $reason = $_POST['reason'];
    $value = $_POST['value'];

    $stmt = $conn->prepare("UPDATE loss_events SET product=?, batch=?, quantity=?, date=?, reason=?, value=? WHERE id=?");
    $stmt->bind_param("ssissdi", $product, $batch, $quantity, $date, $reason, $value, $id);

    if ($stmt->execute()) {
        header("Location: dashboard-template.php");
        exit();
    } else {
        echo "Error updating record.";
    }
}
?>
