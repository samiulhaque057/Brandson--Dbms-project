<?php
// createorder.php

// Includes (DO NOT CHANGE per user instruction)
include 'includes/config.php';
include 'includes/dbConnection.php'; // Include database connection
include 'includes/functions.php'; // Include functions

// Start session for messages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Create New Order"; // Set page title for header
$error_message = '';
$inventoryItems = []; // Array to hold inventory items for the dropdown

// --- Fetch Inventory Items for Dropdown ---
// Fetch inventory items with quantity > 0 to populate the dropdown
// We check for $conn here because we cannot change dbConnection.php to handle connection errors gracefully
if ($conn) {
    $sql_inventory = "SELECT id, name, quantity FROM inventory WHERE quantity > 0 ORDER BY name ASC";
    $result_inventory = $conn->query($sql_inventory);

    if ($result_inventory) {
        if ($result_inventory->num_rows > 0) {
            while ($row = $result_inventory->fetch_assoc()) {
                $inventoryItems[] = $row;
            }
        }
        $result_inventory->free(); // Free result set
    } else {
         // Handle query error
         $error_message = "Database error fetching inventory items: " . $conn->error;
         error_log("MySQL Query Error (createorder.php - inventory fetch): " . $conn->error);
    }
} else {
     $error_message = "Database connection failed. Cannot fetch inventory items.";
}


// --- Handle POST request for creating the order ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && $conn) {
    // Sanitize and validate input
    // Using the sanitize function from functions.php (assuming it exists and works as intended)
    $customerName = isset($_POST['customer_name']) ? sanitize($_POST['customer_name']) : '';
    // Use filter_var for item_id and quantity validation
    $itemId = filter_var($_POST['item_id'] ?? 0, FILTER_VALIDATE_INT); // Using item ID now
    $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_VALIDATE_INT);

    // Basic validation
    if (empty($customerName)) {
        $error_message = "Customer name cannot be empty.";
    } elseif ($itemId === false || $itemId <= 0) {
        $error_message = "Please select a valid item.";
    } elseif ($quantity === false || $quantity <= 0) {
        $error_message = "Order quantity must be a positive number.";
    } else {
        // Inputs are valid, proceed with order creation

        // Get the item details (name and current stock) from the database based on the selected ID
        $stmt_item = $conn->prepare("SELECT name, quantity FROM inventory WHERE id = ? LIMIT 1");
        if ($stmt_item) {
            $stmt_item->bind_param("i", $itemId);
            $stmt_item->execute();
            $result_item = $stmt_item->get_result();
            $item = $result_item->fetch_assoc();
            $stmt_item->close();

            if (!$item) {
                $error_message = "Selected item not found in inventory.";
            } else {
                $itemName = $item['name'];
                $availableStock = $item['quantity'];

                // Check if there is enough stock
                if ($availableStock >= $quantity) {
                    // Generate a simple unique order ID (you might use a more robust method)
                    $orderId = 'PO' . strtoupper(uniqid());

                    // Prepare and execute the INSERT statement for the 'orders' table
                    $stmt_order = $conn->prepare("INSERT INTO orders (order_id, customer_name, item_name, item_id, quantity, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
                    if ($stmt_order) {
                         // "ssisi" indicates: string, string, string, integer, integer
                        $stmt_order->bind_param("ssisi", $orderId, $customerName, $itemName, $itemId, $quantity);

                        if ($stmt_order->execute()) {
                            // Order placed successfully

                            // Optionally, deduct stock immediately upon order creation
                            // A more robust system might deduct stock during processing/shipping
                            // If you deduct here, you need to handle potential failures and rollbacks.
                            // For simplicity in this example, we'll deduct immediately.
                            $stmt_deduct_stock = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                            if ($stmt_deduct_stock) {
                                $stmt_deduct_stock->bind_param("ii", $quantity, $itemId);
                                $stmt_deduct_stock->execute();
                                $stmt_deduct_stock->close();
                            } else {
                                // Log error if stock deduction statement preparation failed
                                error_log("MySQL Prepare Error (createorder.php - stock deduct): " . $conn->error);
                                // Decide how to handle this error - maybe mark order as needing manual stock adjustment
                            }


                            $_SESSION['success_message'] = "Order " . htmlspecialchars($orderId) . " placed successfully for " . htmlspecialchars($customerName) . "!";
                            // Redirect to the dashboard
                            header("Location: productseller.php");
                            exit(); // Stop script execution after redirection
                        } else {
                            // Error during order insertion
                            $error_message = "Error placing order: " . $stmt_order->error;
                            error_log("MySQL Execute Error (createorder.php - order insert): " . $stmt_order->error);
                        }
                        $stmt_order->close();
                    } else {
                        // Error preparing order insertion statement
                        $error_message = "Database error preparing order statement: " . $conn->error;
                        error_log("MySQL Prepare Error (createorder.php - order prepare): " . $conn->error);
                    }

                } else {
                     // Not enough stock
                     $error_message = "Not enough stock available for " . htmlspecialchars($itemName) . ". Available: " . htmlspecialchars($availableStock);
                }
            }
        } else {
             // Error preparing item fetch statement
             $error_message = "Database error fetching item details: " . $conn->error;
             error_log("MySQL Prepare Error (createorder.php - item fetch): " . $conn->error);
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !$conn) {
    // Handle case where form was submitted but DB connection failed
     $error_message = "Database connection failed. Cannot create order.";
}
// If it's a GET request, the form will be displayed below.

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
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card bg-black border-secondary text-white shadow-sm mt-4">
                    <div class="card-header bg-black border-secondary text-white">
                        <h5 class="card-title mb-0 text-white"><i class="bi bi-bag-plus"></i> Create New Order</h5>
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
                             <div class="alert alert-danger text-white">Database connection failed. Cannot create order.</div>
                         <?php endif; ?>

                        <?php if (isset($conn) && $conn): // Only show the form if DB connection is successful ?>
                        <form action="createorder.php" method="POST">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label text-white">Customer Name:</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="customer_name" name="customer_name" required value="<?= isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="item_id" class="form-label text-white">Item:</label>
                                <select class="form-select bg-dark text-white border-secondary" id="item_id" name="item_id" required>
                                    <option value="">-- Select Item --</option>
                                    <?php foreach ($inventoryItems as $invItem): ?>
                                        <option value="<?= htmlspecialchars($invItem['id']) ?>" <?= (isset($_POST['item_id']) && $_POST['item_id'] == $invItem['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($invItem['name']) ?> (<?= htmlspecialchars($invItem['quantity']) ?> in stock)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="quantity" class="form-label text-white">Quantity:</label>
                                <input type="number" class="form-control bg-dark text-white border-secondary" id="quantity" name="quantity" required min="1" value="<?= isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '' ?>">
                            </div>
                            <button type="submit" class="btn btn-primary text-white"><i class="bi bi-bag-plus"></i> Place Order</button>
                             <a href="productseller.php" class="btn btn-secondary ms-2 text-white">Cancel</a>
                        </form>
                        <?php else: ?>
                             <p class="text-center text-white">Cannot create order due to a database connection issue.</p>
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
