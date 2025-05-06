<?php
// Database connection
$host = 'localhost';
$db   = 'brandson';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity_lost = $_POST['quantity_lost'];
    $loss_reason = $_POST['loss_reason'];
    $loss_date = $_POST['loss_date'];
    $action_taken = $_POST['action_taken'];
    $improvement_notes = $_POST['improvement_notes'];

    $stmt = $pdo->prepare("INSERT INTO post_harvest_loss (product_id, quantity_lost, loss_reason, loss_date, action_taken, improvement_notes) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product_id, $quantity_lost, $loss_reason, $loss_date, $action_taken, $improvement_notes]);

    $success_message = "Loss record added successfully!";
}

// Fetch products for dropdown
$products = $pdo->query("SELECT product_id, product_name FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Fetch records for table display and chart
$records = $pdo->query("SELECT p.product_name, l.quantity_lost, l.loss_reason, l.loss_date, l.action_taken, l.improvement_notes 
                        FROM post_harvest_loss l 
                        JOIN products p ON l.product_id = p.product_id 
                        ORDER BY l.loss_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post-Harvest Loss Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>

/* Base Styles */
:root {
    --bg-color: #000000;
    --card-bg: #171717;
    --border-color: #333333;
    --text-color: #ffffff;
    --text-muted: #888888;
    --primary-color: #a855f7;
    --primary-hover: #9333ea;
    --secondary-color: #333333;
    --secondary-hover: #444444;
    --beef-color: #a855f7;
    --chicken-color: #ec4899;
    --lamb-color: #f97316;
    --success-color: #22c55e;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --radius: 8px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    background-color: var(--bg-color);
    color: var(--text-color);
    line-height: 1.5;
}

/* Layout */
.dashboard-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    position: fixed;  /* Fix the sidebar to the left */
    top: 0;
    left: 0;
    height: 100%;
    width: 200px;
    background-color: var(--bg-color);
    border-right: 1px solid var(--border-color);
    padding: 24px;
    z-index: 100; /* Ensure sidebar stays above other content */
}

/* Adjust main content to not overlap the sidebar */
.main-content {
    margin-left: 200px; /* Same width as the sidebar */
    padding-top: 24px;  /* Space for the header */
}

/* Make the top header fixed */
.header {
    position: fixed;  /* Fix the header at the top */
    top: 0;
    left: 200px;  /* To account for the sidebar width */
    width: calc(100% - 200px);  /* Full width minus the sidebar */
    background-color: var(--bg-color);
    padding: 24px;
    border-bottom: 1px solid var(--border-color);
    z-index: 101;  /* Higher than sidebar */
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Adjust the page content for the fixed header */
.dashboard-content {
    margin-top: 80px;  /* Give space for the fixed header */
}

/* Sidebar navigation */
.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 32px;
    align-items: flex-start; /* Align text to the left */
}

.nav-item {
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Align items to start */
    width: 100%;
    height: 48px;
    padding: 8px 16px; /* Add padding for spacing */
    border-radius: var(--radius);
    color: var(--text-muted);
    transition: all 0.2s ease;
}

.nav-item:hover {
    background-color: var(--secondary-color);
}

/* Icon styles */
.nav-item .icon {
    margin-right: 12px; /* Adds space between icon and text */
}

/* User Profile Container */
.user-profile {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

/* Profile Image */
.profile-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid var(--primary-color);
}

/* Profile Name */
.profile-name {
    font-size: 14px;
    color: var(--text-color);
}

/* Dropdown Button */
.dropdown-btn {
    background-color: transparent;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px; /* Added margin for spacing */
}

/* Dropdown Menu */
.dropdown {
    position: relative;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    width: 150px;
    z-index: 102;
}

.dropdown-item {
    padding: 12px;
    text-decoration: none;
    color: var(--text-color);
    display: block;
    font-size: 14px;
}

.dropdown-item:hover {
    background-color: var(--secondary-color);
}

/* Show dropdown menu when triggered */
.dropdown.active .dropdown-menu {
    display: block;
    margin-top: 8px; /* Adds space above the dropdown menu */
}

/* Buttons */
.btn-primary {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.btn-primary:hover {
    background-color: var(--primary-hover);
}

.btn-outline {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background-color: var(--secondary-color);
    color: white;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.btn-outline:hover {
    background-color: var(--secondary-hover);
}

/* Tables */
.inventory-table {
    width: 100%;
    border-collapse: collapse;
}

.inventory-table th,
.inventory-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.inventory-table th {
    font-weight: 500;
    color: var(--text-muted);
}

</style>
</head>
<body class="container mt-4">
    <h2 class="mb-4">Post-Harvest Loss Dashboard</h2>

    <?php if (!empty($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
                <option value="">Choose product</option>
                <?php foreach ($products as $prod): ?>
                    <option value="<?= $prod['product_id'] ?>"><?= $prod['product_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Quantity Lost</label>
            <input type="number" step="0.01" name="quantity_lost" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Reason</label>
            <input type="text" name="loss_reason" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date</label>
            <input type="date" name="loss_date" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Action Taken</label>
            <input type="text" name="action_taken" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Improvement Notes</label>
            <input type="text" name="improvement_notes" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Add Loss Record</button>
        </div>
    </form>

    <h4>Recorded Losses</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity Lost</th>
                <th>Reason</th>
                <th>Date</th>
                <th>Action</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= $r['product_name'] ?></td>
                    <td><?= $r['quantity_lost'] ?></td>
                    <td><?= $r['loss_reason'] ?></td>
                    <td><?= $r['loss_date'] ?></td>
                    <td><?= $r['action_taken'] ?></td>
                    <td><?= $r['improvement_notes'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4 class="mt-5">Monthly Loss Trend</h4>
    <canvas id="lossChart"></canvas>

    <script>
        const ctx = document.getElementById('lossChart').getContext('2d');
        const monthlyData = {};

        <?php foreach ($records as $r): 
            $month = date("Y-m", strtotime($r['loss_date']));
            echo "monthlyData['$month'] = (monthlyData['$month'] ?? 0) + {$r['quantity_lost']};
";
        endforeach; ?>

        const labels = Object.keys(monthlyData);
        const data = Object.values(monthlyData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantity Lost',
                    data: data,
                    fill: false,
                    borderWidth: 2
                }]
            }
        });
    </script>
</body>
</html>
