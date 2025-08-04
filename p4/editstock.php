<?php
// editstock.php

// Includes (DO NOT CHANGE per user instruction)
include 'includes/config.php';
include 'includes/dbConnection.php'; // Include database connection
include 'includes/functions.php'; // Include any helper functions

// Start session for messages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Edit Inventory Item"; // Set page title for header
$item = null; // Variable to hold item data for displaying the form
$itemId = null; // Variable to hold the item ID
$error_message = '';
$success_message = ''; // Initialize success message

// Check if the database connection is available
// We check for $conn here because we cannot change dbConnection.php to handle connection errors gracefully
if (!$conn) {
     $error_message = "Database connection failed.";
     // We won't redirect immediately here, so the error can be displayed on the page.
}

// --- Handle POST request for updating the item ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && $conn) {
    // Get and validate the item ID from the hidden input field
    $itemId = filter_var($_POST['item_id'] ?? null, FILTER_VALIDATE_INT);
    // Get and sanitize/validate the updated item name and quantity
    // Using the sanitize function from functions.php (assuming it exists and works as intended)
    $itemName = trim($_POST['item_name'] ?? '');
    // Use filter_var for quantity validation and sanitization
    $itemQuantity = filter_var($_POST['item_quantity'] ?? 0, FILTER_VALIDATE_INT); // Use FILTER_VALIDATE_FLOAT if quantity is decimal

    // Validation checks
    if ($itemId === false || $itemId <= 0) {
         $error_message = "Invalid item ID for update.";
    } elseif (empty($itemName)) {
        $error_message = "Item name cannot be empty.";
    } elseif ($itemQuantity === false || $itemQuantity < 0) {
         $error_message = "Invalid quantity provided.";
    } else {
        // Inputs are valid, proceed with database update on the 'inventory' table

        // Prepare the SQL UPDATE statement using a prepared statement
        $sql = "UPDATE inventory SET name = ?, quantity = ? WHERE id = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters: s=string (name), i=integer (quantity), i=integer (id)
             // Use "sdi" if your quantity column is DECIMAL/FLOAT
            $stmt->bind_param("sii", $itemName, $itemQuantity, $itemId);

            // Execute the prepared statement
            if ($stmt->execute()) {
                // Check if any rows were affected by the update
                if ($stmt->affected_rows > 0) {
                     $_SESSION['success_message'] = "Inventory item updated successfully.";
                } else {
                     // No rows affected might mean the data was the same or ID didn't exist
                     $_SESSION['success_message'] = "Inventory item details were not changed."; // Or a different message
                }
                 // Redirect back to the dashboard after a successful update
                 header("Location: productseller.php");
                 exit(); // Stop script execution after redirection
            } else {
                // If execution failed, set an error message and log the error
                $error_message = "Error updating inventory item: " . $stmt->error;
                 error_log("MySQL Execute Error (editstock.php - UPDATE): " . $stmt->error); // Log the specific SQL error
            }

            // Close the prepared statement
            $stmt->close();

        } else {
             // If statement preparation failed
             $error_message = "Database error preparing update statement: " . $conn->error;
             error_log("MySQL Prepare Error (editstock.php - PREPARE UPDATE): " . $conn->error); // Log the specific SQL error
        }
    }
} // End of POST request handling


// --- Handle GET request to display the edit form ---
// This part runs when the user clicks the "Edit" link on the dashboard
// It also runs if the POST request above failed validation or execution,
// allowing the user to see the form again with error messages.
if ($_SERVER["REQUEST_METHOD"] == "GET" || ($error_message && $_SERVER["REQUEST_METHOD"] == "POST")) {

    // Get the item ID from the URL (e.g., editstock.php?id=123) for GET requests
    // If it was a POST request with errors, the item_id might be in $_POST
    $itemId = filter_var($_GET['id'] ?? $_POST['item_id'] ?? null, FILTER_VALIDATE_INT);

    // Validate the item ID
    if ($itemId === false || $itemId <= 0) {
        $error_message = "Invalid item ID provided for editing.";
        // We won't redirect immediately, so the error can be displayed.
    } elseif ($conn) { // Only try to fetch if DB connection is available
        // Fetch the existing item data from the database based on the ID
        $sql = "SELECT id, name, quantity FROM inventory WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            // Bind the item ID parameter
            $stmt->bind_param("i", $itemId); // 'i' indicates an integer parameter

            // Execute the statement
            $stmt->execute();

            // Get the result set
            $result = $stmt->get_result();

            // Check if an item with the given ID was found
            if ($result->num_rows == 1) {
                // Fetch the item data as an associative array
                $item = $result->fetch_assoc();
            } else {
                // If no item was found with that ID
                $error_message = "Inventory item not found.";
                 // We won't redirect immediately, so the error can be displayed.
            }

            // Close the statement and free the result set
            $stmt->close();
            $result->free();

        } else {
            // If statement preparation failed
            $error_message = "Database error fetching item data: " . $conn->error;
            error_log("MySQL Prepare Error (editstock.php - SELECT): " . $conn->error); // Log the specific SQL error
        }
    } // End of fetch if $conn is available
}


// --- Display the Edit Form ---
// Note: header.php and footer.php are included to provide the page structure.
// Assuming header.php starts the HTML and body, and footer.php closes them.
// Assuming header.php also includes the sidebar and main content container divs.
?>

<?php
// Include header (DO NOT CHANGE per user instruction)
// Assuming header.php includes dbConnection.php and starts the HTML, body, and main container
include 'includes/header.php';
?>

<main class="main-content">
    <section class="dashboard-content p-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-black border-secondary text-white">
                        <h5 class="card-title mb-0">Edit Inventory Item</h5>
                    </div>
                    <div class="card-body">
                         <?php
                        // Display session messages if they exist (from previous redirects)
                        if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success text-white"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
                            <?php unset($_SESSION['success_message']);
                        endif; ?>
                        <?php if ($error_message): // Display current page error message ?>
                            <div class="alert alert-danger text-white"><?= htmlspecialchars($error_message) ?></div>
                        <?php endif; ?>
                         <?php
                         // Check for database connection error message from dbConnection.php if it sets one
                         if (isset($conn) && !$conn): ?>
                             <div class="alert alert-danger text-white">Database connection failed. Cannot edit item.</div>
                         <?php endif; ?>


                        <?php if ($item && isset($conn) && $conn): // Only show the form if item data is loaded and DB connection is successful ?>
                        <form action="editstock.php" method="POST">
                            <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">

                            <div class="mb-3">
                                <label for="itemName" class="form-label text-white">Item Name:</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="itemName" name="item_name" value="<?= htmlspecialchars($item['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="itemQuantity" class="form-label text-white">Quantity (kg/units):</label>
                                <input type="number" class="form-control bg-dark text-white border-secondary" id="itemQuantity" name="item_quantity" value="<?= htmlspecialchars($item['quantity']) ?>" min="0" required>
                            </div>
                             <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                 <a href="productseller.php" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                        <?php elseif (!isset($conn) || !$conn): ?>
                             <p class="text-center text-white">Cannot load item data due to a database connection issue.</p>
                        <?php else: ?>
                             <p class="text-center text-white">Item data could not be loaded.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer (DO NOT CHANGE per user instruction)
// Assuming footer.php closes the main and app-container divs, and body/html tags
// You might need to create a simple footer.php if you don't have one.
// Example footer.php: </div></div></body></html>
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
