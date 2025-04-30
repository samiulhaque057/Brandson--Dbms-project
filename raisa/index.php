<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agro Farm Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    
    <!-- Chart.js for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>



    <?php include 'includes/config.php'; ?>
    <?php include 'includes/functions.php'; ?>
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

    <div class="dashboard-container">

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
 

<div class="main-content">
<?php include 'includes/header.php'; ?>



            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="content-main">
                    <!-- Overview Card -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Overview</h2>
                            <div class="card-legend">
                                <div class="legend-item">
                                    <span class="legend-dot beef"></span>
                                    <span class="legend-label">Beef</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-dot chicken"></span>
                                    <span class="legend-label">Chicken</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-dot lamb"></span>
                                    <span class="legend-label">Lamb</span>
                                </div>
                                <div class="sort-dropdown">
                                    <span>Sort by</span>
                                    <button class="btn-outline">
                                        Week
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <canvas id="overviewChart" height="300"></canvas>
                        </div>
                    </div>

                    <!-- Inventory Tracking -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Inventory Tracking</h2>
                            <button class="btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Add New Stock
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="tabs">
                                <button class="tab-btn active" data-tab="all">All</button>
                                <button class="tab-btn" data-tab="beef">Beef</button>
                                <button class="tab-btn" data-tab="chicken">Chicken</button>
                                <button class="tab-btn" data-tab="lamb">Lamb</button>
                            </div>
                            <div class="tab-content active" id="all-tab">
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
                                        <?php foreach ($inventoryData as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="meat-type">
                                                    <span class="type-dot <?= strtolower($item['type']) ?>"></span>
                                                    <?= $item['type'] ?>
                                                </div>
                                            </td>
                                            <td><?= $item['batch'] ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= $item['processingDate'] ?></td>
                                            <td><?= $item['expirationDate'] ?></td>
                                            <td><?= $item['location'] ?></td>
                                            <td>
                                                <button class="btn-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                        <circle cx="12" cy="12" r="1"></circle>
                                                        <circle cx="19" cy="12" r="1"></circle>
                                                        <circle cx="5" cy="12" r="1"></circle>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
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

                <!-- Right Sidebar -->
                <div class="content-sidebar">
                    <!-- Total Inventory -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Total Inventory</h2>
                            <button class="btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="stat-value"><?= number_format($totalInventory['value']) ?> kg</div>
                            <div class="stat-change">
                                <span class="badge <?= $totalInventory['change'] > 0 ? 'positive' : 'negative' ?>">
                                    <?= $totalInventory['change'] > 0 ? '+' : '' ?><?= $totalInventory['change'] ?>%
                                </span>
                                from last week
                            </div>
                        </div>
                    </div>

                    <!-- Spoilage Rate -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Spoilage Rate</h2>
                            <button class="btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="stat-value"><?= $spoilageRate['value'] ?>%</div>
                            <div class="stat-change">
                                <span class="badge <?= $spoilageRate['change'] < 0 ? 'positive' : 'negative' ?>">
                                    <?= $spoilageRate['change'] > 0 ? '+' : '' ?><?= $spoilageRate['change'] ?>%
                                </span>
                                from last week
                            </div>
                        </div>
                    </div>

                    <!-- Expiring Soon -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Expiring Soon</h2>
                            <button class="btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="stat-value"><?= number_format($expiringSoon['value']) ?> kg</div>
                            <div class="stat-change">
                                <span class="badge <?= $expiringSoon['change'] < 0 ? 'positive' : 'negative' ?>">
                                    <?= $expiringSoon['change'] > 0 ? '+' : '' ?><?= $expiringSoon['change'] ?>%
                                </span>
                                from last week
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Recent Activity</h2>
                            <button class="btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="activity-list">
                                <?php foreach ($activityData as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?= $activity['type'] ?>">
                                        <?php if ($activity['type'] === 'add'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        <?php elseif ($activity['type'] === 'remove'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <rect x="1" y="3" width="15" height="13"></rect>
                                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                        </svg>
                                        <?php else: ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="activity-details">
                                        <p class="activity-description"><?= $activity['description'] ?></p>
                                        <p class="activity-time"><?= $activity['time'] ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/dashboard.js"></script>
</body>
</html>