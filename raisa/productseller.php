<?php
// Start session for messages (ensure this is at the very top before any output)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Includes (DO NOT CHANGE per user instruction)
include 'includes/config.php';
include 'includes/functions.php';

// Check for success message
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/styles-dashboard2.css">
</head>
<body>
<div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="logo.png" alt="Brandson Logo" width="28" height="28">
                    <span class="brand-name">Brandson</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="../samiul/dashboard.php" class="nav-item ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                    <span class="nav-item-dashboard">Dashboard</span>
                </a>
                <!-- <a href="analytics.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span class="nav-item-name">Analytics</span>
                </a> -->
                <a href="../samiul/add_stock.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <span class="nav-item-name">Stock Entry</span>
                </a>
                <a href="../muaz/dashboard-template.php" class="nav-item">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
        <path d="M12 2C10.89 2 10 2.89 10 4V16.44C8.85 16.72 8 17.97 8 19.3C8 21.03 9.97 23 12 23C14.03 23 16 21.03 16 19.3C16 17.97 15.15 16.72 14 16.44V4C14 2.89 13.11 2 12 2ZM12 20C11.45 20 11 19.55 11 19C11 18.45 11.45 18 12 18C12.55 18 13 18.45 13 19C13 19.55 12.55 20 12 20ZM14 7H10V4C10 3.45 10.45 3 11 3C11.55 3 12 3.45 12 4V7H14V5C14 4.45 13.55 4 13 4C12.45 4 12 4.45 12 5V7Z"></path>
    </svg>
    <span class="nav-item-name">Cold Storage</span>
</a>


<a href="..\jugumaya\dashboard-1.php" class="nav-item">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
        <path d="M10.29 3.86L3.86 10.29a2 2 0 0 0 0 2.83l6.43 6.43a2 2 0 0 0 2.83 0l6.43-6.43a2 2 0 0 0 0-2.83L13.12 3.86a2 2 0 0 0-2.83 0z" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
    </svg>
    <span class="nav-item-name">Loss Auditor</span>
</a>




                <a href="../raisa/productseller.php" class="nav-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span class="nav-item-name">Sales</span>
                </a>
                <a href="../saif/loss_dashboard.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span class="nav-item-name">Preventive Measures</span>
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
                    <input type="text" placeholder="Search inventory, batches..." class="search-input">
                </div>

                <h1 class="page-title">Dashboard</h1>

                <div class="profile-container">
                    <button id="profileButton" class="profile-button">
                        <div class="profile-avatar">JD</div>
                    </button>
                    <div id="profileDropdown" class="profile-dropdown">
                        <a href="#" class="dropdown-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
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

            <div class="dashboard-content">
            <?php
            // dashboard-template.php

            // Includes (DO NOT CHANGE per user instruction)
            // include 'includes/config.php'; // Already included above
            include 'includes/dbConnection.php'; // Include database connection
            // include 'includes/functions.php'; // Already included above (contains static data functions)

            // Start session for messages (ensure this is at the very top before any output - moved above HTML)
            // if (session_status() == PHP_SESSION_NONE) {
            //     session_start();
            // }

            $pageTitle = "Dashboard"; // Set page title for header

            // Check for success message (from other pages) - moved above HTML
            // $success_message = '';
            // if (isset($_SESSION['success_message'])) {
            //     $success_message = $_SESSION['success_message'];
            //     unset($_SESSION['success_message']);
            // }

            // Check for error message (from other pages or connection)
            $error_message = '';
            if (isset($_SESSION['error_message'])) {
                $error_message = $_SESSION['error_message'];
                unset($_SESSION['error_message']);
            }
            // Check for database connection error message from dbConnection.php if it sets one
            if (isset($conn) && !$conn) {
                 $error_message = "Database connection failed. Data might be incomplete or static.";
            }


            // --- Fetch Dashboard Metrics from Database ---
            $totalInventory = 0;
            $nearExpiryInventory = 'N/A'; // Placeholder - requires expiry_date in inventory table
            $spoiledThisWeekKg = 'N/A'; // Placeholder - requires spoilage tracking
            $spoiledThisWeekPercent = 0; // Placeholder
            $todaysOrders = 0;

            // We check for $conn here because we cannot change dbConnection.php to handle connection errors gracefully
            if ($conn) {
                // Fetch Total Inventory
                $sql_total_inventory = "SELECT SUM(quantity) AS total FROM inventory";
                $result_total_inventory = $conn->query($sql_total_inventory);
                if ($result_total_inventory) {
                    if ($row = $result_total_inventory->fetch_assoc()) {
                        $totalInventory = $row['total'] ?? 0;
                    }
                    $result_total_inventory->free();
                } else {
                    error_log("MySQL Query Error (dashboard-template.php - total inventory): " . $conn->error);
                }

                // Fetch Today's Orders (Requires an 'order_date' column in orders table)
                $sql_todays_orders = "SELECT COUNT(*) AS total FROM orders WHERE DATE(order_date) = CURDATE()";
                $result_todays_orders = $conn->query($sql_todays_orders);
                if ($result_todays_orders) {
                    if ($row = $result_todays_orders->fetch_assoc()) {
                        $todaysOrders = $row['total'] ?? 0;
                    }
                    $result_todays_orders->free();
                } else {
                    error_log("MySQL Query Error (dashboard-template.php - todays orders): " . $conn->error);
                }

                // --- Fetch Inventory and Order Data for Table from Database ---
                $inventory_data = [];
                $sql_inventory = "SELECT id, name, quantity FROM inventory ORDER BY name ASC"; // Select necessary columns
                $result_inventory = $conn->query($sql_inventory);

                if ($result_inventory) {
                    if ($result_inventory->num_rows > 0) {
                        while ($row = $result_inventory->fetch_assoc()) {
                            $inventory_data[] = $row;
                        }
                    }
                    $result_inventory->free();
                } else {
                    error_log("MySQL Query Error (dashboard-template.php - inventory data): " . $conn->error);
                }

                $placed_orders = [];
                $sql_placed_orders = "SELECT  order_id, customer_id, type, quantity, status FROM orders WHERE status = 'Pending' ORDER BY order_date DESC"; // Select necessary columns
                $result_placed_orders = $conn->query($sql_placed_orders);

                if ($result_placed_orders) {
                    if ($result_placed_orders->num_rows > 0) {
                        while ($row = $result_placed_orders->fetch_assoc()) {
                            $placed_orders[] = $row;
                        }
                    }
                    $result_placed_orders->free();
                } else {
                    error_log("MySQL Query Error (dashboard-template.php - placed orders): " . $conn->error);
                }

                $delivered_orders = [];
                $sql_delivered_orders = "SELECT  order_id, customer_id, type, quantity, status FROM orders WHERE status = 'Delivered' ORDER BY order_date DESC"; // Select necessary columns
                $result_delivered_orders = $conn->query($sql_delivered_orders);

                if ($result_delivered_orders) {
                    if ($result_delivered_orders->num_rows > 0) {
                        while ($row = $result_delivered_orders->fetch_assoc()) {
                            $delivered_orders[] = $row;
                        }
                    }
                    $result_delivered_orders->free();
                } else {
                    error_log("MySQL Query Error (dashboard-template.php - delivered orders): " . $conn->error);
                }

                // --- Data for Pie Chart (Uses Fetched Inventory Data) ---
                // Prepare data for the Chart.js pie chart
                $total_inventory_quantity_chart = array_sum(array_column($inventory_data, 'quantity'));
                $inventory_labels = json_encode(array_column($inventory_data, 'name'));
                $inventory_quantities = json_encode(array_column($inventory_data, 'quantity'));

            } else {
                // If connection failed, use static data from functions.php for metrics and table display
                // Note: functions.php static data might not match the table structure expected below.
                // This is a fallback due to the constraint of not changing functions.php.
                $staticInventoryData = getInventoryData(); // Get static data
                $totalInventory = calculateTotalInventory($staticInventoryData)['value'];
                $todaysOrders = count(getActivityData()); // Using activity count as a rough placeholder for orders
                $inventory_data = $staticInventoryData; // Use static data for table
                $placed_orders = []; // No static order data provided in functions.php
                $delivered_orders = []; // No static order data provided in functions.php

                // Prepare static data for the Chart.js pie chart
                 $total_inventory_quantity_chart = array_sum(array_column($staticInventoryData, 'quantity'));
                 $inventory_labels = json_encode(array_column($staticInventoryData, 'type')); // Use 'type' for static data
                 $inventory_quantities = json_encode(array_column($staticInventoryData, 'quantity'));

            }


            // Note: header.php and footer.php are included to provide the page structure.
            // Assuming header.php starts the HTML and body, and footer.php closes them.
            // Assuming header.php also includes the sidebar and main content container divs.
            // The original code structure seems to duplicate some of the main structure parts.
            // I will format the provided code block as a standalone file, assuming it's the main dashboard content.
            // The original code includes a section that looks like it should be within header.php and footer.php.
            // I will keep the original structure but note that typical practice would separate these.

            // The original code includes header.php and then has a section that looks like
            // the content that *should* be in header.php (app-container, sidebar, main-content, header).
            // Then it has the dashboard-content div and the main PHP logic.
            // Then it includes footer.php and adds script tags.

            // Assuming the user wants *this specific code block* aligned and kept together,
            // despite the structural oddity of including header/footer and then redefining parts of the layout.
            // I will align the code as it is provided.

            ?>

            <?php
            // Include header (DO NOT CHANGE per user instruction)
            // Assuming header.php includes dbConnection.php and starts the HTML, body, and main container
            // Note: This inclusion is structurally unusual given the HTML structure already present.
            // Keeping it as requested.
            // include 'includes/header.php'; // The original code includes this commented out or implicitly. I will follow the provided structure below this comment block.
            ?>

            <section class="dashboard-content p-3">
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
                         <div class="alert alert-danger text-white">Database connection failed. Data might be incomplete or static.</div>
                     <?php endif; ?>


                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-black border-primary shadow-sm text-white h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h6 class="metric-label">Total Inventory</h6>
                                    <h4 class="metric-value"><?= htmlspecialchars($totalInventory) ?> kg/units</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-black border-warning shadow-sm text-white h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h6 class="metric-label">Near Expiry (48 hrs)</h6>
                                    <h4 class="metric-value"><?= htmlspecialchars($nearExpiryInventory) ?> kg/units</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-black border-danger shadow-sm text-white h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h6 class="metric-label">Spoiled This Week</h6>
                                    <h4 class="metric-value"><?= htmlspecialchars($spoiledThisWeekKg) ?> kg (<?= number_format($spoiledThisWeekPercent, 1) ?>%)</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-black border-success shadow-sm text-white h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h6 class="metric-label">Today's Orders</h6>
                                    <h4 class="metric-value"><?= htmlspecialchars($todaysOrders) ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                             <div class="card bg-black border-secondary text-white shadow-sm h-100">
                                 <div class="card-header bg-black border-secondary text-white">
                                     <i class="bi bi-pie-chart-fill"></i> Inventory Overview
                                 </div>
                                 <div class="card-body">
                                     <?php if ($total_inventory_quantity_chart > 0): ?>
                                     <canvas id="inventoryPieChart" height="250"></canvas> <script>
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
                                                      borderColor: '#343a40' // Border color for slices
                                                  }]
                                              },
                                              options: {
                                                  responsive: true,
                                                  maintainAspectRatio: false, // Allow height adjustment
                                                  plugins: {
                                                      legend: {
                                                          position: 'bottom',
                                                          labels: {
                                                              color: '#ced4da' // Light color for legend text
                                                          }
                                                      },
                                                      tooltip: {
                                                           backgroundColor: '#343a40', // Dark tooltip background
                                                           titleColor: '#fff', // White title
                                                           bodyColor: '#ced4da', // Light body text
                                                           borderColor: '#495057', // Slightly lighter border
                                                           borderWidth: 1,
                                                          callbacks: {
                                                              label: function(context) {
                                                                  let label = context.label || '';
                                                                  if (context.parsed !== null) {
                                                                      // Determine unit based on context if possible, default to 'units' or 'kg'
                                                                      label += ': ' + context.parsed + ' units/kg';
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
                        <div class="col-md-6 mb-3">
                            <div class="card bg-black border-secondary text-white shadow-sm h-100">
                                <div class="card-header bg-black border-secondary text-white">
                                    <i class="bi bi-list-ul"></i> Recent Activity
                                </div>
                                <div class="card-body text-white">
                                    <?php $recentActivity = getActivityData(); // Get static activity data ?>
                                    <?php if (!empty($recentActivity)): ?>
                                        <ul class="list-unstyled">
                                            <?php foreach ($recentActivity as $activity): ?>
                                                 <li class="mb-2">
                                                     <small class="text-muted"><?= htmlspecialchars($activity['time']) ?></small><br>
                                                     <span class="text-info"><?= htmlspecialchars(ucfirst($activity['type'])) ?>:</span> <?= htmlspecialchars($activity['description']) ?>
                                                 </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-center text-white">No recent activity to display yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card bg-black border-secondary mb-3 shadow-sm text-white">
                        <div class="card-header bg-black border-secondary text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-white"><i class="bi bi-table"></i> Inventory and Order Details</h5>
                             <div>
                                  <a href="add_stock.php" class="btn btn-success text-white btn-sm"><i class="bi bi-plus-square"></i> Add Inventory</a>
                                  <a href="createorder.php" class="btn btn-primary ms-2 text-white btn-sm"><i class="bi bi-plus-circle"></i> Create Order</a>
                             </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0 inventory-order-table">
                                    <thead>
                                        <tr>
                                            <th class="text-white">ID</th>
                                            <th class="text-white">Type</th>
                                            <th class="text-white">Product/Customer</th>
                                            <th class="text-white">Quantity</th>
                                            <th class="text-white">Status</th>
                                            <th class="text-white">Actions</th>
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
                                                     <td class="text-white">INV-<?= htmlspecialchars($item['id']) ?></td> <td class="text-white">Inventory</td>
                                                     <td class="text-white"><?= htmlspecialchars($item['name']) ?></td>
                                                     <td class="text-white"><?= htmlspecialchars($item['quantity']) ?></td>
                                                     <td class="text-white">In Stock</td>
                                                     <td class="text-white action-buttons">
                                                          <a href="editstock.php?id=<?= htmlspecialchars($item['id']) ?>" class="btn btn-primary btn-sm" title="Edit">
                                                              <i class="bi bi-pencil"></i> Edit
                                                          </a>
                                                          <form action="delete_stock.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                              <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                                              <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                  <i class="bi bi-trash"></i> Delete
                                                              </button>
                                                          </form>
                                                     </td>
                                                 </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($placed_orders as $order): ?>
                                                 <tr>
                                                     <td class="text-white"><?= htmlspecialchars($order['order_id']) ?></td>
                                                     <td class="text-white">Order Placed</td>
                                                     <td class="text-white"><?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['item_name']) ?>)</td> <td class="text-white"><?= htmlspecialchars($order['quantity']) ?></td>
                                                     <td class="text-white"><?= htmlspecialchars($order['status']) ?></td>
                                                     <td class="text-white action-buttons">
                                                        <form action="process_order.php" method="POST" style="display:inline;">
                                                             <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>"> <input type="hidden" name="new_status" value="Delivered">
                                                             <button type="submit" class="btn btn-info btn-sm process-order-btn" title="Process Order">
                                                                 <i class="bi bi-truck"></i> Process/Deliver
                                                             </button>
                                                         </form>
                                                          <form action="cancel_order.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                               <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>"> <button type="submit" class="btn btn-danger btn-sm" title="Cancel Order">
                                                                   <i class="bi bi-x-circle"></i> Cancel
                                                               </button>
                                                           </form>
                                                     </td>
                                                 </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($delivered_orders as $order): ?>
                                                 <tr>
                                                     <td class="text-white"><?= htmlspecialchars($order['order_id']) ?></td>
                                                     <td class="text-white">Order Delivered</td>
                                                      <td class="text-white"><?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['item_name']) ?>)</td> <td class="text-white"><?= htmlspecialchars($order['quantity']) ?></td>
                                                     <td class="text-white"><?= htmlspecialchars($order['status']) ?></td>
                                                      <td class="text-white action-buttons">
                                                         <button class="btn btn-success btn-sm delivered-order-btn" disabled title="Delivered">
                                                              <i class="bi bi-check-circle-fill"></i> Delivered
                                                          </button>
                                                     </td>
                                                 </tr>
                                            <?php endforeach; ?>
                                        <?php endif; // End if empty data ?>
                                    </tbody>
                                </table>
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
            // Note: This inclusion is structurally unusual given the HTML structure already present.
            // Keeping it as requested.
            // include 'includes/footer.php'; // The original code includes this commented out or implicitly. I will follow the provided structure after this comment block.
            ?>
             </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
         <script>
             // JavaScript for toggling the profile dropdown (Already in header.php, but included here for completeness)
             document.addEventListener('DOMContentLoaded', function() {
                 const profileButton = document.getElementById('profileButton');
                 const profileDropdown = document.getElementById('profileDropdown');

                 if(profileButton && profileDropdown) {
                     profileButton.addEventListener('click', function(event) {
                         event.stopPropagation(); // Prevent click from closing immediately
                         profileDropdown.classList.toggle('show');
                     });

                     // Close the dropdown if the user clicks outside of it
                     window.addEventListener('click', function(event) {
                         if (!event.target.matches('#profileButton') && !event.target.closest('.profile-button')) {
                             if (profileDropdown.classList.contains('show')) {
                                 profileDropdown.classList.remove('show');
                             }
                         }
                     });
                 }

                 // Add confirmation for delete buttons (using event delegation)
                 // The confirmation is already handled by the form's onsubmit="return confirm(...);"
                 // This script block is mainly for the profile dropdown and can be extended for other JS needs.
                 // document.addEventListener('DOMContentLoaded', function() { }); // Already have DOMContentLoaded listener
             });

         </script>
    </body>
</html>