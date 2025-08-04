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

    <?php 
        // Get inventory data
        $inventoryData = getInventoryData();
        $lossData = getLossData();
        $activityData = getActivityData();

        // Calculate stats
        $totalInventory = calculateTotalInventory($inventoryData);
        $spoilageRate = calculateSpoilageRate($lossData);
        $expiringSoon = calculateExpiringSoon($inventoryData);
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
                <a href="analytics.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span class="nav-item-name">Analytics</span>
                </a>
                <a href="add_stock.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <span class="nav-item-name">Stock Entry</span>
                </a>
                <a href="dashboard-template.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span class="nav-item-name">Cold Storage</span>
                </a>
                <a href="#" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span class="nav-item-name">Settings</span>
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

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
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
                        <div class="stat-info">
                            <span class="stat-label">Total Inventory</span>
                            <h2 class="stat-value">1,750 kg</h2>
                            <div class="stat-change positive">
                                <span class="change-badge">+3.2%</span>
                                <span class="change-period">from last week</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pink">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Spoilage Rate</span>
                            <h2 class="stat-value">2.4%</h2>
                            <div class="stat-change positive">
                                <span class="change-badge">-0.5%</span>
                                <span class="change-period">from last week</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Expiring Soon</span>
                            <h2 class="stat-value">320 kg</h2>
                            <div class="stat-change negative">
                                <span class="change-badge">+12%</span>
                                <span class="change-period">from last week</span>
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
                            <h3>Overview</h3>
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
                                <h1>1,750 kg</h1>
                                <div class="stat-change positive">
                                    <span class="change-badge">+3.2%</span>
                                    <span class="change-period">from last week</span>
                                </div>
                            </div>
                            
                            <div class="inventory-breakdown">
                                <div class="breakdown-item">
                                    <div class="breakdown-header">
                                        <span>Beef</span>
                                        <span>45%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar purple" style="width: 45%"></div>
                                    </div>
                                </div>
                                
                                <div class="breakdown-item">
                                    <div class="breakdown-header">
                                        <span>Chicken</span>
                                        <span>32%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar pink" style="width: 32%"></div>
                                    </div>
                                </div>
                                
                                <div class="breakdown-item">
                                    <div class="breakdown-header">
                                        <span>Lamb</span>
                                        <span>18%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar orange" style="width: 18%"></div>
                                    </div>
                                </div>
                                
                                <div class="breakdown-item">
                                    <div class="breakdown-header">
                                        <span>Other</span>
                                        <span>5%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar gray" style="width: 5%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Stock Additions Chart (Moved to top) -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Stock Additions per Month</h3>
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
                            <h3>Inventory Breakdown</h3>
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
                    <div class="table-filters">
                        <div class="filter-buttons">
                            <button class="filter-btn active">All</button>
                            <button class="filter-btn">Beef</button>
                            <button class="filter-btn">Chicken</button>
                            <button class="filter-btn">Lamb</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Meat Type</th>
                                    <th>Batch #</th>
                                    <th>Quantity (kg)</th>
                                    <th>Processing Date</th>
                                    <th>Expiration Date</th>
                                    <th>Storage Location</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="meat-type">
                                            <span class="meat-indicator purple"></span>
                                            Beef
                                        </div>
                                    </td>
                                    <td>B-1234</td>
                                    <td>450</td>
                                    <td>2023-04-15</td>
                                    <td>2023-05-15</td>
                                    <td>Cold Storage A</td>
                                    <td>
                                        <button class="row-menu-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="meat-type">
                                            <span class="meat-indicator pink"></span>
                                            Chicken
                                        </div>
                                    </td>
                                    <td>C-5678</td>
                                    <td>320</td>
                                    <td>2023-04-16</td>
                                    <td>2023-05-01</td>
                                    <td>Cold Storage B</td>
                                    <td>
                                        <button class="row-menu-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="meat-type">
                                            <span class="meat-indicator orange"></span>
                                            Lamb
                                        </div>
                                    </td>
                                    <td>L-9012</td>
                                    <td>180</td>
                                    <td>2023-04-14</td>
                                    <td>2023-05-10</td>
                                    <td>Cold Storage A</td>
                                    <td>
                                        <button class="row-menu-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                 <!-- Post-Harvest Loss Recording -->
                 <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Post-Harvest Loss Recording</h2>
                            <button class="btn-outline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                View Reports
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="loss-grid">
                                <?php foreach ($lossData as $item): ?>
                                <div class="loss-card">
                                    <div class="loss-info">
                                        <h3><?= $item['type'] ?></h3>
                                        <div class="loss-meta">
                                            <span class="badge outline"><?= $item['batch'] ?></span>
                                            <span class="date"><?= $item['date'] ?></span>
                                        </div>
                                    </div>
                                    <div class="loss-percentage">
                                        <div class="percentage"><?= $item['lossPercentage'] ?>%</div>
                                        <div class="label">Loss</div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>


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

            // Inventory Breakdown Doughnut Chart
            const breakdownCtx = document.getElementById('inventoryBreakdownChart').getContext('2d');
            const breakdownChart = new Chart(breakdownCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Beef', 'Chicken', 'Lamb', 'Other'],
                    datasets: [{
                        data: [45, 32, 18, 5],
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
                                    return `${context.label}: ${context.raw}%`;
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
                        label: 'Stock Additions (kg)',
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
