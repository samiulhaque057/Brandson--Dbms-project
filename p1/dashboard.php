<?php
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/stylestest.css">
    <link rel="stylesheet" href="css/modal.css">

    <?php
// Get inventory data from the database
$inventoryData = getInventoryData($conn);
$lossData = getLossData();
$activityData = getActivityData();

// Calculate total inventory
$totalInventory = calculateTotalInventory($conn);

// Calculate spoilage rate (total kg after expiration date)
$spoilageRate = calculateSpoilageRate($inventoryData);

// Calculate expiring soon inventory (expire in 2 days)
$expiringSoon = calculateExpiringSoon($inventoryData);

// Extract values for use in the HTML
$totalQuantity = $totalInventory['value'];  // Total inventory
$change = $totalInventory['change'];  // Change percentage

$spoiledQuantity = $spoilageRate['value'];  // Spoiled inventory total kg
$spoiledChange = $spoilageRate['change'];  // Change in spoilage

$expiringQuantity = $expiringSoon['value'];  // Expiring soon inventory total kg
$expiringChange = $expiringSoon['change'];  // Change in expiring soon

//Inventory card//

// Get Inventory Data - Don't overwrite existing variables
if (!isset($inventory)) {
    $inventory = getInventoryBreakdown($conn);
}


/////////////////////////////////////////////////////////////////////////



//debug


?>

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
                <a href="dashboard.php" class="nav-item active">
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
                <a href="add_stock.php" class="nav-item">
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




                <a href="../raisa/productseller.php" class="nav-item">
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

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
            <div class="search-container" style="display: flex; align-items: center; gap: 8px; background-color: #000;">
                    <div style="position: relative; display: flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 10px; color: #888;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="search-input" placeholder="Search batch or type" class="search-input" style="
                            background-color: #111;
                            color: #fff;
                            border: 1px solid #444;
                            padding: 8px 12px 8px 34px;
                            border-radius: 6px;
                            font-size: 14px;
                            outline: none;
                            width: 100%;
                            max-width: 380px; /* Adjust width as necessary */
                        ">
                    </div>
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

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </div>
                        <!-- Total Inventory Card -->
                        <div class="stat-info">
                            <span class="stat-label">Total Inventory</span>
                            <h2 class="stat-value"><?php echo number_format($totalQuantity); ?> kg</h2>
                            <div class="stat-change <?php echo ($change >= 0) ? 'positive' : 'negative'; ?>">
                            <span class="change-period">Total amount of meat in stock</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pink">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                        </div>
                        <!-- Spoilage Rate Card -->
<div class="stat-info">
    <span class="stat-label">Spoilage Rate</span>
    <h2 class="stat-value"><?php echo number_format($spoiledQuantity); ?> kg</h2>
    <div class="stat-change <?php echo ($spoiledChange >= 0) ? 'positive' : 'negative'; ?>">
    <span class="change-period">Expired</span>
    </div>
</div>
                    </div>
                    
                    <!-- Expiring Soon Inventory Card -->
<div class="stat-card">
    <div class="stat-icon orange">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
    </div>
    <!-- Expiring Soon Inventory Card -->

    <div class="stat-info">
        <span class="stat-label">Expiring Soon</span>
        <h2 class="stat-value"><?php echo number_format($expiringQuantity); ?> kg</h2>
        <div class="stat-change <?php echo ($expiringChange >= 0) ? 'positive' : 'negative'; ?>">
            
            <span class="change-period">Expiring within 2 days</span>
        </div>
    </div>
</div>

                    
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Orders This Week</span>
                            <h2 class="stat-value">42</h2>
                            <div class="stat-change positive">
                                <span class="change-badge">+8%</span>
                                <span class="change-period">from last week</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-grid">
                    <!-- Overview Chart -->
                    <div class="chart-card overview-chart">
                        <div class="chart-header">
                            <h3>Sales chart</h3>
                            <div class="chart-controls">
                                <div class="chart-legend">
                                    <span class="legend-item"><span class="legend-color purple"></span>Beef</span>
                                    <span class="legend-item"><span class="legend-color pink"></span>Chicken</span>
                                    <span class="legend-item"><span class="legend-color orange"></span>Lamb</span>
                                </div>
                                <select class="time-select">
                                    <option>Week</option>
                                    <option>Month</option>
                                    <option>Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="overviewChart"></canvas>
                        </div>
                    </div>

            <!-- Total Inventory Card -->
            <div class="chart-card inventory-total">
    <div class="chart-header">
        <h3>Total Inventory</h3>
    </div>
    <div class="chart-body">
        <div class="inventory-total-value">
            <h1><?php echo formatNumber($inventory['total']); ?> kg</h1>
            <div class="stat-change positive">
                <!-- <span class="change-badge">+<?php echo $inventory['change']; ?>%</span> -->
                <span class="change-period">Breakdown by type:</span>
            </div>
        </div>
        
        <div class="inventory-breakdown">
            <?php 
            // Define a color mapping for the 'type' values
            $colorMap = [
                'Beef' => 'purple',
                'Chicken' => 'pink',
                'Lamb' => 'orange',
                'Other' => 'gray' // Adjust or add more categories if needed
            ];

            // Loop through each category in the breakdown
            foreach ($inventory['breakdown'] as $type => $data): 
                // Get the color class for the type
                $colorClass = isset($colorMap[$type]) ? $colorMap[$type] : 'default-color'; // Default color if not found
            ?>
                <div class="breakdown-item">
                    <div class="breakdown-header">
                        <span><?php echo $type; ?></span>
                        <!-- Display percentage and kg -->
                        <span><?php echo $data['percentage']; ?>% / <?php echo formatNumber($data['quantity']); ?>kg</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar <?php echo $colorClass; ?>" style="width: <?php echo $data['percentage']; ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


                    

                    

                    <!-- Stock Additions Chart (Moved to top) -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Stock addition chart</h3>
                            <button class="chart-menu-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="chart-body">
                            <canvas id="stockAdditionsChart"></canvas>
                        </div>
                    </div>


                    

                    <!-- Inventory Breakdown Chart (Moved to bottom) -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Batch Breakdown</h3>
                            <button class="chart-menu-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="chart-body">
                            <canvas id="inventoryBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>    


                
                    

<!-- Inventory Table -->
<div class="table-card">
    <div class="table-header">
        <h3>Inventory Tracking</h3>
        <a href="add_stock.php" class="btn-add">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Stock
        </a>
    </div>

    <!-- Filter Buttons -->
    <div class="table-filters">
        <div class="filter-buttons">
            <button class="filter-btn active" id="filter-all">All</button>
            <button class="filter-btn" id="filter-beef">Beef</button>
            <button class="filter-btn" id="filter-chicken">Chicken</button>
            <button class="filter-btn" id="filter-lamb">Lamb</button>
        </div>
    </div>



    <table class="inventory-table">
    <thead>
        <tr>
            <th>Meat Type</th>
            <th>Batch #</th>
            <th>Quantity (kg)</th>
            <th>Processing Date</th>
            <th>Expiration Date</th>
            <th>Storage Location</th>
            <th>Total Cost</th> <!-- New column -->
            <th>Actions</th> <!-- Actions column for the edit button -->
        </tr>
    </thead>
    <tbody id="inventory-table-body">
        <?php
        include 'includes/config.php';

        // Fetch all inventory data from the database, including cost
        $sql = "SELECT batch_id, type, quantity, processing_date, expiration_date, location, cost FROM stockData ORDER BY date_added DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $meatClass = '';
                switch ($row['type']) {
                    case 'Beef': $meatClass = 'purple'; break;
                    case 'Chicken': $meatClass = 'pink'; break;
                    case 'Lamb': $meatClass = 'orange'; break;
                    case 'Pork': $meatClass = 'blue'; break;
                    case 'Fish': $meatClass = 'green'; break;
                }

                // Calculate total cost
                $totalCost = $row['quantity'] * $row['cost'];

                echo "<tr class='inventory-row' data-type='" . $row['type'] . "' data-batch='" . $row['batch_id'] . "'>
                        <td><div class='meat-type'>
                                <span class='meat-indicator " . $meatClass . "'></span>" . $row['type'] . "</div></td>
                        <td>" . $row['batch_id'] . "</td>
                        <td>" . $row['quantity'] . " kg</td>
                        <td>" . $row['processing_date'] . "</td>
                        <td>" . $row['expiration_date'] . "</td>
                        <td>" . $row['location'] . "</td>
                        <td>$" . number_format($totalCost, 2) . "</td>
                        <td><button class='row-menu-btn' onclick='openEditModal(\"" . $row['batch_id'] . "\")'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                <circle cx='12' cy='12' r='1'></circle>
                                <circle cx='19' cy='12' r='1'></circle>
                                <circle cx='5' cy='12' r='1'></circle>
                            </svg>
                        </button></td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>No stock data available</td></tr>";
        }
        ?>
    </tbody>
</table>

    </div>
</div>

<!-- JavaScript for Real-Time Search -->
<script>
    // Function to filter inventory rows based on search query
    document.getElementById('search-input').addEventListener('input', function () {
        const searchQuery = this.value.toLowerCase();
        const rows = document.querySelectorAll('.inventory-row');

        rows.forEach(row => {
            const batch = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const type = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const quantity = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const location = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

            if (batch.includes(searchQuery) || type.includes(searchQuery) || quantity.includes(searchQuery) || location.includes(searchQuery)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>



<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditModal()">&times;</span>
        <h3>Edit Stock</h3>
        <form id="editForm">
            <label for="batch">Batch #:</label>
            <input type="text" id="editBatch" name="batch" required readonly>

            <label for="quantity">Quantity (kg):</label>
            <input type="number" id="editQuantity" name="quantity" required>

            <label for="processing-date">Processing Date:</label>
            <input type="date" id="editProcessingDate" name="processing_date" required>

            <label for="expiration-date">Expiration Date:</label>
            <input type="date" id="editExpirationDate" name="expiration_date" required>

            <label for="location">Storage Location:</label>
            <input type="text" id="editLocation" name="location" required>

            <div class="modal-buttons">
                
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteStock()">Delete Stock</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>


    <!-- Demo script to show/hide modal -->
    <script>
        // Function to open the edit modal
        function openEditModal(batch, quantity, processingDate, expirationDate, location) {
            document.getElementById('editBatch').value = batch || 'B-12345';
            document.getElementById('editQuantity').value = quantity || 250;
            document.getElementById('editProcessingDate').value = processingDate || '2023-04-15';
            document.getElementById('editExpirationDate').value = expirationDate || '2023-07-15';
            document.getElementById('editLocation').value = location || 'Cold Storage A';
            
            document.getElementById('editModal').style.display = 'block';
        }

        // Function to close the edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        
    </script>


        <!-- JavaScript for Filter and Modal -->
        <script>
            // Filter Logic
            document.getElementById('filter-all').addEventListener('click', function() {
                filterInventory('All');
            });
            document.getElementById('filter-beef').addEventListener('click', function() {
                filterInventory('Beef');
            });
            document.getElementById('filter-chicken').addEventListener('click', function() {
                filterInventory('Chicken');
            });
            document.getElementById('filter-lamb').addEventListener('click', function() {
                filterInventory('Lamb');
            });

            function filterInventory(meatType) {
                const rows = document.querySelectorAll('.inventory-row');
                rows.forEach(row => {
                    if (meatType === 'All' || row.dataset.type === meatType) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Edit Modal Logic
            let selectedBatch;

            function openEditModal(batch) {
                selectedBatch = batch;
                const row = document.querySelector(`[data-batch="${batch}"]`);
                document.getElementById('editBatch').value = row.cells[1].textContent;
                document.getElementById('editQuantity').value = row.cells[2].textContent;
                document.getElementById('editProcessingDate').value = row.cells[3].textContent;
                document.getElementById('editExpirationDate').value = row.cells[4].textContent;
                document.getElementById('editLocation').value = row.cells[5].textContent;
                document.getElementById('editModal').style.display = 'block';
            }

            function closeEditModal() {
                document.getElementById('editModal').style.display = 'none';
            }

            // Handle form submission to save the changes
            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();

                // Collect the data from the form
                const batch = document.getElementById('editBatch').value;
                const quantity = document.getElementById('editQuantity').value;
                const processingDate = document.getElementById('editProcessingDate').value;
                const expirationDate = document.getElementById('editExpirationDate').value;
                const location = document.getElementById('editLocation').value;

                // Prepare the data for AJAX request
                const data = {
                    batch: batch,
                    quantity: quantity,
                    processing_date: processingDate,
                    expiration_date: expirationDate,
                    location: location
                };

                // AJAX request to update the database
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_stock.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Update the table row without reloading the page
                            updateTableRow(batch, response.updatedData);
                            closeEditModal();
                        } else {
                            alert('Failed to update the data.');
                        }
                    }
                };
                xhr.send(JSON.stringify(data));
            });

            // Function to update the table row after successful update
            function updateTableRow(batch, updatedData) {
                const row = document.querySelector(`[data-batch="${batch}"]`);
                row.cells[2].textContent = updatedData.quantity + " kg";
                row.cells[3].textContent = updatedData.processing_date;
                row.cells[4].textContent = updatedData.expiration_date;
                row.cells[5].textContent = updatedData.location;
            }


            // Function to handle deleting stock
function deleteStock() {
    if (!selectedBatch) {
        alert("No stock selected for deletion.");
        return;
    }

    // Confirm deletion
    if (confirm("Are you sure you want to delete this stock?")) {
        // Prepare data to send with the request
        const data = {
            batch: selectedBatch // Send the selected batch to delete
        };

        // AJAX request to delete the stock from the database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_stock.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json'); // Ensure the request is sent as JSON
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText); // Response from PHP
                if (response.success) {
                    // If successful, remove the row from the table
                    const row = document.querySelector(`[data-batch="${selectedBatch}"]`);
                    if (row) {
                        row.remove();
                    }
                    closeEditModal(); // Close the modal after deletion
                } else {
                    alert('Failed to delete stock: ' + response.error);
                }
            } else {
                alert('Error occurred during deletion');
            }
        };
        xhr.send(JSON.stringify(data)); // Send data as JSON (not form data)
    }
}

        </script>






        </main>
    </div>

    

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Overview Line Chart
            const overviewCtx = document.getElementById('overviewChart').getContext('2d');
            const overviewChart = new Chart(overviewCtx, {
                type: 'line',
                data: {
                    labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    datasets: [
                        {
                            label: 'Beef',
                            data: [3000, 2600, 3200, 4800, 4000, 3700, 4000],
                            borderColor: '#a855f7',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Chicken',
                            data: [1200, 1800, 2500, 3000, 2800, 3200, 2000],
                            borderColor: '#ec4899',
                            backgroundColor: 'rgba(236, 72, 153, 0.1)',
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Lamb',
                            data: [800, 1400, 1800, 1500, 1800, 3000, 3000],
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            tension: 0.4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#0a0a0a',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#333',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#adb5bd'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#adb5bd'
                            }
                        }
                    }
                }
            });

            // Inventory Breakdown Doughnut 
            <?php
                    // Assuming you have a valid database connection
                    include 'includes/config.php';

                    // SQL query to get the count of each meat type
                    $sql = "SELECT type, COUNT(*) as count FROM stockData GROUP BY type";
                    $result = $conn->query($sql);

                    // Prepare the data to be passed to JavaScript
                    $meatData = [
                        'Beef' => 0,
                        'Chicken' => 0,
                        'Lamb' => 0,
                        'Other' => 0
                    ];

                    // Loop through the results and update the meatData array
                    while ($row = $result->fetch_assoc()) {
                        if (array_key_exists($row['type'], $meatData)) {
                            $meatData[$row['type']] = (int)$row['count'];
                        }
                    }

                    // Convert the PHP array to JSON and pass it to JavaScript
                    $meatDataJson = json_encode($meatData);
            ?>
            const metaData = <?php echo $meatDataJson; ?>;
            
            const breakdownCtx = document.getElementById('inventoryBreakdownChart').getContext('2d');
const breakdownChart = new Chart(breakdownCtx, {
    type: 'doughnut',
    data: {
        labels: ['Beef', 'Chicken', 'Lamb', 'Other'],
        datasets: [{
            data: [metaData.Beef, metaData.Chicken, metaData.Lamb, metaData.Other],
            backgroundColor: [
                '#a855f7',
                '#ec4899',
                '#f97316',
                '#6b7280'
            ],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#ffffff',
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: '#0a0a0a',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#333',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        // Calculate the total value for all segments
                        const total = context.dataset.data.reduce((sum, currentValue) => sum + currentValue, 0);
                        
                        // Calculate percentage for the current segment
                        const percentage = ((context.raw / total) * 100).toFixed(2);
                        
                        // Return the label with the percentage
                        return `${context.label}: ${percentage}%`;
                    }
                }
            }
        }
    }
});

            // Stock Additions Bar Chart
            const stockAdditionsCtx = document.getElementById('stockAdditionsChart').getContext('2d');
            const stockAdditionsChart = new Chart(stockAdditionsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Stock Sold (kg)',
                        data: [350, 420, 380, 500, 600, 550, 450, 580, 650, 700, 680, 550],
                        backgroundColor: '#ec4899',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0a0a0a',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#333',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#adb5bd'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#adb5bd'
                            }
                        }
                    }
                }
            });

            // Handle window resize to make charts responsive
            window.addEventListener('resize', function() {
                overviewChart.resize();
                breakdownChart.resize();
                stockAdditionsChart.resize();
            });
        });
    </script>





<script>
// PHP injected data
var labels = <?php echo $labelsJson; ?>;
var temperatureData = <?php echo $temperaturesJson; ?>;
var humidityData = <?php echo $humiditiesJson; ?>;

// Temperature Chart
var ctxTemp = document.getElementById('temperatureChart').getContext('2d');
var temperatureChart = new Chart(ctxTemp, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Temperature (°C)',
            data: temperatureData,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Cold Storage'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Temperature (°C)'
                }
            }
        }
    }
});

// Humidity Chart
var ctxHum = document.getElementById('humidityChart').getContext('2d');
var humidityChart = new Chart(ctxHum, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Humidity (%)',
            data: humidityData,
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Cold Storage'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Humidity (%)'
                }
            }
        }
    }
});
</script>







    <!-- Profile Dropdown Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');
        
            // Toggle dropdown when profile button is clicked
            profileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });
        
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
