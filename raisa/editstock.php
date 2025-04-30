<?php
$pageTitle = "Edit Stock";
include 'includes/header.php'; // Includes dbConnection.php
// require_once 'includes/dbConnection.php'; // Ensure connection is available
require_once 'includes/functions.php'; // Include functions

$error_message = '';
$item = null; // To hold the fetched item data

// Check if item ID is provided in the URL
if (isset($_GET['id'])) {
    $itemId = (int)$_GET['id'];

    // Fetch the existing item data
    $stmt = $conn->prepare("SELECT id, name, quantity FROM inventory WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $item = $result->fetch_assoc();
    } else {
        // Item not found
        $_SESSION['error_message'] = "Inventory item not found.";
        redirect('productseller.php');
    }

    $stmt->close();

} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission for updating
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $itemName = isset($_POST['item_name']) ? sanitizeInput($conn, $_POST['item_name']) : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

     if ($itemId <= 0) {
         $error_message = "Invalid item ID.";
     } elseif (empty($itemName)) {
        $error_message = "Item name cannot be empty.";
    } elseif ($quantity < 0) { // Allow 0 for out of stock
        $error_message = "Quantity cannot be negative.";
    } else {
        // Prepare and execute the UPDATE statement
        $stmt = $conn->prepare("UPDATE inventory SET name = ?, quantity = ? WHERE id = ?");
        $stmt->bind_param("sii", $itemName, $quantity, $itemId);

        if ($stmt->execute()) {
            // Success
            $_SESSION['success_message'] = "Inventory item '" . htmlspecialchars($itemName) . "' updated successfully!";
            redirect('productseller.php'); // Redirect to dashboard
        } else {
            // Error
            $error_message = "Error updating inventory item: " . $stmt->error;

             // Re-fetch item data to display the form with errors
             $stmt = $conn->prepare("SELECT id, name, quantity FROM inventory WHERE id = ? LIMIT 1");
             $stmt->bind_param("i", $itemId);
             $stmt->execute();
             $result = $stmt->get_result();
             if ($result->num_rows == 1) {
                 $item = $result->fetch_assoc();
             } else {
                 $_SESSION['error_message'] = "Inventory item not found after update attempt.";
                 redirect('productseller.php');
             }
             $stmt->close();
        }
    }

} else {
    // No ID provided and not a POST request
    $_SESSION['error_message'] = "No inventory item specified for editing.";
    redirect('productseller.php');
}

// Only display the form if $item data was fetched
if ($item):
?>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card bg-black border-secondary text-white shadow-sm mt-4">
                            <div class="card-header bg-black border-secondary text-white">
                                <h5 class="card-title mb-0 text-white"><i class="bi bi-pencil"></i> Edit Inventory Item</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger text-white"><?= $error_message ?></div>
                                <?php endif; ?>

                                <form action="editstock.php" method="POST">
                                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <div class="mb-3">
                                        <label for="item_name" class="form-label text-white">Item Name:</label>
                                        <input type="text" class="form-control bg-dark text-white border-secondary" id="item_name" name="item_name" value="<?= htmlspecialchars($item['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label text-white">Quantity (kg/units):</label>
                                        <input type="number" class="form-control bg-dark text-white border-secondary" id="quantity" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" required min="0">
                                    </div>
                                    <button type="submit" class="btn btn-primary text-white"><i class="bi bi-save"></i> Update Stock</button>
                                     <a href="productseller.php" class="btn btn-secondary ms-2 text-white">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

<?php
endif; // Close the if($item) block
// This closes the main and app-container divs opened in header.php
include 'includes/footer.php'; // We'll create a simple footer.php next
?>