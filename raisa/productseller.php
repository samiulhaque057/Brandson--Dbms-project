<?php
include 'includes/config.php';
include 'includes/functions.php';

// Start the session if not already started (needed for success messages)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check for success message from other pages (like processing orders or editing inventory)
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
}

// --- Database connection (assuming you have this in config.php) ---
// Make sure your config.php establishes the $conn connection.
// Example (if not in config.php, uncomment and configure):
/*
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
*/

// --- Sample Inventory Data (Replace with actual database queries) ---
// In a real application, you would fetch this from your database
$inventory_data = [
    ['id' => 1, 'name' => 'Pork', 'quantity' => 150],
    ['id' => 2, 'name' => 'Beef', 'quantity' => 200],
    ['id' => 3, 'name' => 'Lamb', 'quantity' => 75],
    ['id' => 4, 'name' => 'Poultry', 'quantity' => 250],
];

// --- Sample Order Data (Replace with actual database queries) ---
// In a real application, you would fetch this from your database
$placed_orders = [
    ['order_id' => 'PO001', 'customer' => 'Butcher Shop A', 'item' => 'Beef', 'quantity' => 10, 'status' => 'Pending'],
    ['order_id' => 'PO002', 'customer' => 'Restaurant B', 'item' => 'Pork', 'quantity' => 15, 'status' => 'Pending'],
];

$delivered_orders = [
    ['order_id' => 'DO003', 'customer' => 'Grocery Store C', 'item' => 'Poultry', 'quantity' => 50, 'status' => 'Delivered'],
    ['order_id' => 'DO004', 'customer' => 'Catering Service D', 'item' => 'Lamb', 'quantity' => 20, 'status' => 'Delivered'],
];

// Note: The loss entry POST handling from the previous code snippet is removed
// as this file is now focused on displaying inventory and orders,
// similar to the first dashboard example you provided.

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Seller Dashboard - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/styles3.css"> <style>
        /* Add or adjust styles as needed for the table */
        .inventory-order-table th, .inventory-order-table td {
            vertical-align: middle;
        }
        .order-button {
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
        }
        .process-order-btn {
            background-color: #007bff; /* Primary blue */
            color: white;
        }
        .delivered-order-btn {
            background-color: #28a745; /* Success green */
            color: white;
        }
        /* Styles for the profile dropdown */
        .profile-container {
            position: relative;
            display: inline-block;
        }

        .profile-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            background-color: #007bff; /* Example avatar background */
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #343a40; /* Dark background for dropdown */
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            min-width: 150px;
        }

        .profile-dropdown a {
            color: #ced4da; /* Light text color */
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #454d55; /* Divider */
        }

        .profile-dropdown a:hover {
            background-color: #495057; /* Slightly lighter background on hover */
            color: #fff; /* White text on hover */
        }

        .profile-dropdown a:last-child {
            border-bottom: none;
        }

        .profile-dropdown.show {
            display: block;
        }

        /* Fix alignment issues */
        .app-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .sidebar {
            flex-shrink: 0; /* Prevent sidebar from shrinking */
            width: 240px; /* Adjust as needed */
            /* Add other sidebar styles if necessary */
        }

        .main-content {
            flex-grow: 1; /* Allow main content to take remaining space */
            display: flex;
            flex-direction: column;
        }

        .header {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Add other header styles if necessary */
        }

        .dashboard-content {
            padding: 1rem;
            flex-grow: 1; /* Ensure content takes up available vertical space */
        }

        .row {
            display: flex;
            flex-wrap: wrap; /* Allow items to wrap on smaller screens */
            margin-left: -15px; /* Adjust based on your grid system */
            margin-right: -15px; /* Adjust based on your grid system */
        }

        .col-md-3, .col-md-6 {
            padding-left: 15px; /* Adjust based on your grid system */
            padding-right: 15px; /* Adjust based on your grid system */
        }

        @media (min-width: 768px) {
            .col-md-3 {
                flex: 0 0 auto;
                width: 25%;
            }
            .col-md-6 {
                flex: 0 0 auto;
                width: 50%;
            }
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="logo.png" alt="Brandson Logo" width="28" height="28">
                    <span class="brand-name">Brandson</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                    <span class="nav-item-dashboard">Dashboard</span>
                </a>
                <a href="update_stock.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span class="nav-item-name">Stock Entry</span>
                </a>
                <a href="#" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span class="nav-item-name">Log Out</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="search-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" placeholder="Search inventory, orders..." class="search-input">
                </div>

                <h1 class="page-title">Dashboard</h1>

                <div class="profile-container">
                    <button id="profileButton" class="profile-button">
                        <div class="profile-avatar">JD</div>
                    </button>
                    <div id="profileDropdown" class="profile-dropdown">
                        <a href="#" class="dropdown-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Settings
                        </a>
                        <a href="#" class="dropdown-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </header>

            <section class="dashboard-content p-3">
                <?php if ($success_message): ?>
                    <div class="alert alert-success text-white"><?= $success_message ?></div>
                <?php endif; ?>


                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-black border-primary shadow-sm text-white">
                            <div class="card-body">
                                <h6 class="metric-label">Total Inventory</h6>
                                <h4 class="metric-value"><?= isset($totalInventory) ? $totalInventory : 'Loading...' ?> kg/units</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-black border-warning shadow-sm text-white">
                            <div class="card-body">
                                <h6 class="metric-label">Near Expiry (48 hrs)</h6>
                                <h4 class="metric-value"><?= isset($nearExpiryInventory) ? $nearExpiryInventory : 'Loading...' ?> kg/units</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-black border-danger shadow-sm text-white">
                            <div class="card-body">
                                <h6 class="metric-label">Spoiled This Week</h6>
                                <h4 class="metric-value"><?= isset($spoiledThisWeekKg) ? $spoiledThisWeekKg : 'Loading...' ?> kg (<?= isset($spoiledThisWeekPercent) ? number_format($spoiledThisWeekPercent, 1) : '0' ?>%)</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-black border-success shadow-sm text-white">
                            <div class="card-body">
                                <h6 class="metric-label">Today's Orders</h6>
                                <h4 class="metric-value"><?= isset($todaysOrders) ? $todaysOrders : 'Loading...' ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-black border-secondary text-white shadow-sm">
                            <div class="card-header bg-black border-secondary text-white">
                                <i class="bi bi-pie-chart-fill"></i> Inventory Overview
                            </div>
                            <div class="card-body">
                                <?php
                                    // Calculate total inventory for a potential chart (optional, based on previous code)
                                    $total_inventory = array_sum(array_column($inventory_data, 'quantity'));
                                    $inventory_labels = json_encode(array_column($inventory_data, 'name'));
                                    $inventory_quantities = json_encode(array_column($inventory_data, 'quantity'));
                                ?>
                                <?php if ($total_inventory > 0): ?>
                                <canvas id="inventoryPieChart" height="200"></canvas>
                                <script>
                                    const pieCtx = document.getElementById('inventoryPieChart').getContext('2d');
                                    new Chart(pieCtx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?= $inventory_labels ?>,
                                            datasets: [{
                                                data: <?= $inventory_quantities ?>,
                                                backgroundColor: [
                                                    '#17a2b8', '#28a745', '#ffc107', '#dc3545', '#007bff', '#6c757d',
                                                    '#f8f9fa', '#343a40', '#e83e8c', '#fd7e14'
                                                    // Add more colors as needed
                                                ],
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    position: 'bottom',
                                                    labels: {
                                                        color: '#ced4da' // Light color for legend text
                                                    }
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            let label = context.label || '';
                                                            if (context.parsed !== null) {
                                                                label += ': ' + context.parsed + ' units'; // or ' kg' depending on your unit
                                                            }
                                                            return label;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <?php else: ?>
                                    <p class="text-center text-white">No inventory data available to display chart.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-black border-secondary text-white shadow-sm">
                            <div class="card-header bg-black border-secondary text-white">
                                <i class="bi bi-list-ul"></i> Recent Activity
                            </div>
                            <div class="card-body text-white">
                                <p>No recent activity to display yet.</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card bg-black border-secondary mb-3 shadow-sm text-white">
                    <div class="card-header bg-black border-secondary text-white">
                        <h5 class="card-title mb-0 text-white"><i class="bi bi-table"></i> Inventory and Order Details</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 inventory-order-table">
                                <thead>
                                    <tr>
                                        <th class="text-white">Item/Order ID</th>
                                        <th class="text-white">Type</th>
                                        <th class="text-white">Product/Customer</th>
                                        <th class="text-white">Quantity</th>
                                        <th class="text-white">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($inventory_data) && empty($placed_orders) && empty($delivered_orders)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-white">No inventory or order data available.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($inventory_data as $item): ?>
                                            <tr>
                                                <td class="text-white">INV-<?= htmlspecialchars(strtoupper(substr(md5($item['name']), 0, 8))) ?></td>
                                                <td class="text-white">Inventory</td>
                                                <td class="text-white"><?= htmlspecialchars($item['name']) ?></td>
                                                <td class="text-white"><?= htmlspecialchars($item['quantity']) ?></td>
                                                <td class="text-white">In Stock</td>
                                                <td class="text-white">
                                            
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php foreach ($placed_orders as $order): ?>
                                            <tr>
                                                <td class="text-white"><?= htmlspecialchars($order['order_id']) ?></td>
                                                <td class="text-white">Order Placed</td>
                                                <td class="text-white"><?= htmlspecialchars($order['customer']) ?></td>
                                                <td class="text-white"><?= htmlspecialchars($order['quantity']) ?></td>
                                                <td class="text-white"><?= htmlspecialchars($order['status']) ?></td>
                                                <td class="text-white">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php foreach ($delivered_orders as $order): ?>
                                            <tr>
                                                <td class="text-white"><?= htmlspecialchars($order['order_id']) ?></td>
                                                <td class="text-white">Order Delivered</td>
                                                <td class="text-white"><?= htmlspecialchars($order['customer']) ?></td>
                                                <td class="text-white"><?= htmlspecialchars($order['quantity']) ?></td>
                                                <td class="text-white"><?= htmlspecialchars($order['status']) ?></td>
                                                <td class="text-white">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
     <script>
        // JavaScript for toggling the profile dropdown
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');

        profileButton.addEventListener('click', function() {
            profileDropdown.classList.toggle('show');
        });

        window.addEventListener('click', function(event) {
            if (!event.target.matches('#profileButton') && !event.target.closest('.profile-button')) {
                if (profileDropdown.classList.contains('show')) {
                    profileDropdown.classList.remove('show');
                }
            }
        });
  
    </script>
    </body>
</html>