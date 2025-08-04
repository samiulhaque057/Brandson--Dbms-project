<?php
include 'includes/config.php';
include 'includes/dbConnection.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $date_time = $_POST['date_time'];
    $facility = $_POST['facility'];
    $stage = $_POST['stage'];
    $product_type = $_POST['product_type'];
    $quantity_lost = $_POST['quantity_lost'];
    $loss_reason = $_POST['loss_reason'];

    // Insert the data into the database
    $sql = "INSERT INTO loststock (date_time, facility, stage, product_type, quantity_lost, loss_reason) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssds", $date_time, $facility, $stage, $product_type, $quantity_lost, $loss_reason);
        if ($stmt->execute()) {
            $success_message = "Loss data added successfully!";
            // Redirect to dashboard after successful submission
            header("Location: dashboard-1.php");
            exit; // Ensure that the script stops after redirection
        } else {
            $error_message = "Error adding loss data.";
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Loss Data - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="mt-5">Add Loss Data</h1>

    <!-- Display success/error messages -->
    <?php if (isset($success_message)) { echo '<div class="alert alert-success">' . $success_message . '</div>'; } ?>
    <?php if (isset($error_message)) { echo '<div class="alert alert-danger">' . $error_message . '</div>'; } ?>

    <!-- Data entry form -->
    <form method="POST" action="addloss.php">
        <div class="mb-3">
            <label for="date_time" class="form-label">Date & Time</label>
            <input type="datetime-local" class="form-control" id="date_time" name="date_time" required>
        </div>
        <div class="mb-3">
            <label for="facility" class="form-label">Facility</label>
            <input type="text" class="form-control" id="facility" name="facility" required>
        </div>
        <div class="mb-3">
            <label for="stage" class="form-label">Stage</label>
            <input type="text" class="form-control" id="stage" name="stage" required>
        </div>
        <div class="mb-3">
            <label for="product_type" class="form-label">Product Type</label>
            <input type="text" class="form-control" id="product_type" name="product_type" required>
        </div>
        <div class="mb-3">
            <label for="quantity_lost" class="form-label">Quantity Lost (kg)</label>
            <input type="number" step="0.01" class="form-control" id="quantity_lost" name="quantity_lost" required>
        </div>
        <div class="mb-3">
            <label for="loss_reason" class="form-label">Loss Reason</label>
            <textarea class="form-control" id="loss_reason" name="loss_reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="dashboard-1.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
