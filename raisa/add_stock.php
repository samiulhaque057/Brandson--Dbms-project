<?php
$pageTitle = "Add New Stock";
include 'includes/header.php'; // Includes dbConnection.php and functions.php

$error_message = '';

// Check if the form was submitted AND database connection is valid
if ($_SERVER["REQUEST_METHOD"] == "POST" && $conn) {
    // Sanitize and validate input
    $itemName = isset($_POST['item_name']) ? Input($conn, $_POST['item_name']) : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0; // Cast to integer, basic validation

    if (empty($itemName)) {
        $error_message = "Item name cannot be empty.";
    } elseif ($quantity <= 0) {
        $error_message = "Quantity must be a positive number.";
    } else {
        // Prepare and execute the INSERT statement
        $stmt = $conn->prepare("INSERT INTO inventory (name, quantity) VALUES (?, ?)");
        // Check if statement preparation was successful
        if ($stmt) {
            $stmt->bind_param("si", $itemName, $quantity); // "s" for string, "i" for integer

            if ($stmt->execute()) {
                // Success
                $_SESSION['success_message'] = "Inventory item '" . htmlspecialchars($itemName) . "' added successfully!";
                redirect('productseller.php'); // Redirect to dashboard
            } else {
                // Error during execution
                $error_message = "Error adding inventory item: " . $stmt->error;
            }

            $stmt->close();
        } else {
            // Error preparing statement
            $error_message = "Database error: Could not prepare statement.";
            error_log("MySQL Prepare Error (add_stock.php): " . $conn->error);
        }
    }
}
?>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card bg-black border-secondary text-white shadow-sm mt-4">
                            <div class="card-header bg-black border-secondary text-white">
                                <h5 class="card-title mb-0 text-white"><i class="bi bi-plus-square"></i> Add New Inventory Item</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($db_error): // Display database connection error ?>
                                    <div class="alert alert-danger text-white"><?= $db_error ?></div>
                                <?php endif; ?>
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger text-white"><?= $error_message ?></div>
                                <?php endif; ?>

                                <?php if ($conn): // Only show the form if DB connection is successful ?>
                                <form action="add_stock.php" method="POST">
                                    <div class="mb-3">
                                        <label for="item_name" class="form-label text-white">Item Name:</label>
                                        <input type="text" class="form-control bg-dark text-white border-secondary" id="item_name" name="item_name" required value="<?= isset($_POST['item_name']) ? htmlspecialchars($_POST['item_name']) : '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label text-white">Quantity (kg/units):</label>
                                        <input type="number" class="form-control bg-dark text-white border-secondary" id="quantity" name="quantity" required min="1" value="<?= isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '' ?>">
                                    </div>
                                    <button type="submit" class="btn btn-success text-white"><i class="bi bi-plus-circle"></i> Add Stock</button>
                                     <a href="productseller.php" class="btn btn-secondary ms-2 text-white">Cancel</a>
                                </form>
                                <?php else: ?>
                                     <p class="text-center text-white">Cannot add stock due to a database connection issue.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

<?php
// This closes the main and app-container divs opened in header.php
include 'includes/footer.php';
?>