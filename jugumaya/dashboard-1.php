<?php
include 'includes/config.php'; // Include your config (for SITE_NAME, etc.)
include 'includes/dbConnection.php'; // Include your database connection
include 'includes/functions.php'; // Include any helper functions
session_start(); // Start session for messages

// Check for success message (keep this)
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
// Add info/error messages as well
$info_message = '';
if (isset($_SESSION['info_message'])) {
    $info_message = $_SESSION['info_message'];
    unset($_SESSION['info_message']);
}
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}


// --- Data Fetching for Cards and Chart ---

// Get filter values from GET request
$startDate = $_GET['start-date'] ?? '';
$endDate = $_GET['end-date'] ?? '';
$meatType = $_GET['meat-type'] ?? 'All';
$facility = $_GET['facility'] ?? 'All';
$searchTerm = $_GET['search-term'] ?? ''; // Get the search term

// Base query for aggregated data from loststock table
$sqlAggregate = "SELECT stage, SUM(quantity_lost) AS total_lost FROM loststock";

$where_clauses = [];
$params = [];
$param_types = "";

if (!empty($startDate)) {
    $where_clauses[] = "date_time >= ?";
    $params[] = $startDate . " 00:00:00";
    $param_types .= "s";
}
if (!empty($endDate)) {
    $where_clauses[] = "date_time <= ?";
    $params[] = $endDate . " 23:59:59";
    $param_types .= "s";
}
if ($meatType != 'All') {
    $where_clauses[] = "product_type = ?";
    $params[] = $meatType;
    $param_types .= "s";
}
if ($facility != 'All') {
    $where_clauses[] = "facility = ?";
    $params[] = $facility;
    $param_types .= "s";
}

// Add search term filtering to the aggregate query as well
if (!empty($searchTerm)) {
    // Search across relevant columns
    $where_clauses[] = "(facility LIKE ? OR stage LIKE ? OR product_type LIKE ? OR loss_reason LIKE ?)";
    $likeTerm = '%' . $searchTerm . '%';
    $params[] = $likeTerm;
    $params[] = $likeTerm;
    $params[] = $likeTerm;
    $params[] = $likeTerm;
    $param_types .= "ssss";
}


if (count($where_clauses) > 0) {
    $sqlAggregate .= " WHERE " . implode(" AND ", $where_clauses);
}

$sqlAggregate .= " GROUP BY stage";

// Fetch data for cards and chart
$lossDataAggregate = [];
if ($stmtAggregate = $conn->prepare($sqlAggregate)) {
    if (count($params) > 0) {
        // Use call_user_func_array or the ... splat operator for binding if PHP version >= 5.6
        $stmtAggregate->bind_param($param_types, ...$params);
    }

    $stmtAggregate->execute();
    $resultAggregate = $stmtAggregate->get_result();

    while ($row = $resultAggregate->fetch_assoc()) {
        $lossDataAggregate[$row['stage']] = $row['total_lost'];
    }
    $stmtAggregate->close();
} else {
    $error_message = "Error fetching aggregated data: " . $conn->error;
}

// Prepare data for Chart.js
$chartLabels = array_keys($lossDataAggregate);
$chartData = array_values($lossDataAggregate);
$chartColors = [
    'Slaughter' => '#0d6efd',
    'Processing' => '#fd7e14',
    'Storage' => '#e83e8c',
    'Handling' => '#f8d7da',
    'Transport' => '#adb5bd',
    'Rejected' => '#ffc107',
    'Spoiled' => '#dc3545',
];
$chartBackgroundColor = [];
foreach($chartLabels as $label) {
    $chartBackgroundColor[] = $chartColors[$label] ?? '#6c757d';
}


// --- Data Fetching for the Table ---

// Define base SQL query for the table data from loststock table
$sqlTable = "SELECT id, date_time, facility, stage, product_type, quantity_lost, loss_reason FROM loststock";

// Use the same filtering logic as the aggregate query, including the search term
$where_clauses_table = [];
$params_table = [];
$param_types_table = "";

// Rebuild where clauses and parameters for the table query
if (!empty($startDate)) {
    $where_clauses_table[] = "date_time >= ?";
    $params_table[] = $startDate . " 00:00:00";
    $param_types_table .= "s";
}
if (!empty($endDate)) {
    $where_clauses_table[] = "date_time <= ?";
    $params_table[] = $endDate . " 23:59:59";
    $param_types_table .= "s";
}
if ($meatType != 'All') {
    $where_clauses_table[] = "product_type = ?";
    $params_table[] = $meatType;
    $param_types_table .= "s";
}
if ($facility != 'All') {
    $where_clauses_table[] = "facility = ?";
    $params_table[] = $facility;
    $param_types_table .= "s";
}

// Add search term filtering to the table query
if (!empty($searchTerm)) {
    $where_clauses_table[] = "(facility LIKE ? OR stage LIKE ? OR product_type LIKE ? OR loss_reason LIKE ?)";
    $likeTermTable = '%' . $searchTerm . '%';
    $params_table[] = $likeTermTable;
    $params_table[] = $likeTermTable;
    $params_table[] = $likeTermTable;
    $params_table[] = $likeTermTable;
    $param_types_table .= "ssss";
}


if (count($where_clauses_table) > 0) {
    $sqlTable .= " WHERE " . implode(" AND ", $where_clauses_table);
}

$sqlTable .= " ORDER BY date_time DESC"; // Add ordering

// Fetch data for the table
$tableData = [];
if ($stmtTable = $conn->prepare($sqlTable)) {
    if (count($params_table) > 0) {
        // Use call_user_func_array or the ... splat operator for binding
        $stmtTable->bind_param($param_types_table, ...$params_table);
    }

    $stmtTable->execute();
    $resultTable = $stmtTable->get_result();

    if ($resultTable->num_rows > 0) {
        while($row = $resultTable->fetch_assoc()) {
            $tableData[] = $row;
        }
    }

    $stmtTable->close();
} else {
    $error_message = "Error fetching table data: " . $conn->error;
}


$conn->close(); // Close connection at the very end of the script
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
<<<<<<< HEAD
        <!-- Sidebar -->
=======
        <?php // include 'includes/sidebar.php'; ?>
>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="logo.png" alt="Brandson Logo" width="28" height="28">
                    <span class="brand-name">Brandson</span>
                </div>
            </div>
<<<<<<< HEAD
            
            <nav class="sidebar-nav">
                <a href="../samiul/dashboard.php" class="nav-item ">
=======

            <nav class="sidebar-nav">
                <a href="dashboard-1.php" class="nav-item active">
>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                    <span class="nav-item-dashboard">Dashboard</span>
                </a>
<<<<<<< HEAD
                <!-- <a href="analytics.php" class="nav-item">
=======
                <a href="analytics.php" class="nav-item">
>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span class="nav-item-name">Analytics</span>
<<<<<<< HEAD
                </a> -->
                <a href="../samiul/add_stock.php" class="nav-item">
=======
                </a>
                <a href="add_stock.php" class="nav-item">
>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
<<<<<<< HEAD
                    <span class="nav-item-name">Stock Entry</span>
                </a>
                <a href="../muaz/dashboard-template.php" class="nav-item">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
        <path d="M12 2C10.89 2 10 2.89 10 4V16.44C8.85 16.72 8 17.97 8 19.3C8 21.03 9.97 23 12 23C14.03 23 16 21.03 16 19.3C16 17.97 15.15 16.72 14 16.44V4C14 2.89 13.11 2 12 2ZM12 20C11.45 20 11 19.55 11 19C11 18.45 11.45 18 12 18C12.55 18 13 18.45 13 19C13 19.55 12.55 20 12 20ZM14 7H10V4C10 3.45 10.45 3 11 3C11.55 3 12 3.45 12 4V7H14V5C14 4.45 13.55 4 13 4C12.45 4 12 4.45 12 5V7Z"></path>
    </svg>
    <span class="nav-item-name">Cold Storage</span>
</a>


<a href="..\jugumaya\dashboard-1.php" class="nav-item active">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
        <path d="M10.29 3.86L3.86 10.29a2 2 0 0 0 0 2.83l6.43 6.43a2 2 0 0 0 2.83 0l6.43-6.43a2 2 0 0 0 0-2.83L13.12 3.86a2 2 0 0 0-2.83 0z" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
    </svg>
    <span class="nav-item-name">Loss Auditor</span>
</a>




=======
                    <span class="nav-item-name">Loss Entry</span>
                </a>
                <a href="#" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span class="nav-item-name">Cold Storage</span>
                </a>
>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc
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
<<<<<<< HEAD
            
=======

>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc

        <main class="main-content">
            <?php // include 'includes/header.php'; ?>
            <header class="header">

            
                 <form method="GET" action="dashboard-1.php" class="search-filter-form">
                    <div class="search-container">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                         <input type="text" placeholder="Search by facility, stage, reason..." value="<?= htmlspecialchars($searchTerm) ?>" class="search-input" name="search-term">
                    </div>



                    <div class="filter-elements-hidden">
                        <input type="hidden" name="start-date" value="<?= htmlspecialchars($startDate) ?>">
                        <input type="hidden" name="end-date" value="<?= htmlspecialchars($endDate) ?>">
                        <input type="hidden" name="meat-type" value="<?= htmlspecialchars($meatType) ?>">
                        <input type="hidden" name="facility" value="<?= htmlspecialchars($facility) ?>">
                        <button type="submit" class="btn btn-primary">Apply Filters / Search</button>
                        <a href="dashboard-1.php" class="btn btn-secondary">Reset Filters</a>
                    </div>

                </form>
                
<<<<<<< HEAD
                <h1 class="page-title">Loss Auditor</h1>
=======
                <h1 class="page-title">LossAuditor</h1>
>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc
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




            <?php
            // Display general success/info/error messages from other pages
            if ($success_message) {
                echo '<div class="alert alert-success m-3">' . htmlspecialchars($success_message) . '</div>';
            }
            if ($info_message) {
                echo '<div class="alert alert-info m-3">' . htmlspecialchars($info_message) . '</div>';
            }
            if ($error_message) {
                echo '<div class="alert alert-danger m-3">' . htmlspecialchars($error_message) . '</div>';
            }
            ?>

       

            <section class="dashboard-cards row p-3">
                <?php
                // Define card colors (match these with your CSS or hardcoded styles)
                $cardColors = [
                    'Slaughter' => ['bg' => '#0d6efd', 'text' => 'white'],
                    'Processing' => ['bg' => '#fd7e14', 'text' => 'white'],
                    'Storage' => ['bg' => '#e83e8c', 'text' => 'white'],
                    'Handling' => ['bg' => '#f8d7da', 'text' => '#842029'], // Use original color/text
                    'Transport' => ['bg' => '#adb5bd', 'text' => 'black'],
                    'Rejected' => ['bg' => '#ffc107', 'text' => 'black'],
                    'Spoiled' => ['bg' => '#dc3545', 'text' => 'white'],
                    // Add other stages and their colors
                ];

                // Loop through the stages and display a card for each
                // Use the keys from the fetched aggregate data if you only want cards for stages with losses
                // Or use a predefined list if you want a card for every possible stage
                $stagesToDisplay = array_keys($lossDataAggregate); // Only show cards for stages with data

                // If you want a fixed set of cards regardless of data, use this instead:
                // $stagesToDisplay = ['Slaughter', 'Processing', 'Storage', 'Handling', 'Transport', 'Rejected', 'Spoiled'];

                foreach ($stagesToDisplay as $stageName) {
                    $totalLost = $lossDataAggregate[$stageName] ?? 0; // Get total lost for this stage, default to 0 if not found
                    $color = $cardColors[$stageName]['bg'] ?? '#6c757d'; // Default background grey
                    $textColor = $cardColors[$stageName]['text'] ?? 'white'; // Default text white
                    ?>
                    <div class="col-md-2">
                        <div class="card text-bg-light mb-3" style="background-color: <?= htmlspecialchars($color) ?>; color: <?= htmlspecialchars($textColor) ?>;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($stageName) ?> Loss</h5>
                                <p><?= htmlspecialchars(number_format($totalLost, 2)) ?> kg</p>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </section>

            <section class="chart-container p-3">
                <canvas id="lossBarChart" height="100"></canvas>
                <script>
                    // Pass PHP data to JavaScript
                    const chartLabels = <?= json_encode($chartLabels) ?>;
                    const chartData = <?= json_encode($chartData) ?>;
                    const chartBackgroundColor = <?= json_encode($chartBackgroundColor) ?>;

                    const ctx = document.getElementById('lossBarChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels, // Use dynamic labels
                            datasets: [{
                                label: 'Total Loss (kg)', // Label reflects kg
                                data: chartData, // Use dynamic data
                                backgroundColor: chartBackgroundColor, // Use dynamic colors
                                borderColor: chartBackgroundColor, // Use same color for border
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true, // Make chart responsive
                            maintainAspectRatio: false, // Allow height to be controlled by CSS/attribute
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Total Loss (kg)' // Label changed
                                    },
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false // Hide dataset label if only one dataset
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += context.parsed.y + ' kg';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            </section>

<<<<<<< HEAD
            



            <!-- Button to trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLossDataModal">
    Add Loss Data
</button>

<!-- Modal structure -->
<div class="modal fade" id="addLossDataModal" tabindex="-1" aria-labelledby="addLossDataModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-light"> <!-- Dark background and light text -->
      <div class="modal-header">
        <h5 class="modal-title" id="addLossDataModalLabel">Add Loss Data</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button> <!-- White close button -->
      </div>
      <div class="modal-body">
        <!-- Form goes here -->
        <form method="POST" action="addloss.php">
            <div class="mb-3">
                <label for="date_time" class="form-label">Date & Time</label>
                <input type="datetime-local" class="form-control bg-dark text-light" id="date_time" name="date_time" required>
            </div>
            <div class="mb-3">
                <label for="facility" class="form-label">Facility</label>
                <input type="text" class="form-control bg-dark text-light" id="facility" name="facility" required>
            </div>
            <div class="mb-3">
    <label for="stage" class="form-label">Stage</label>
    <select class="form-select bg-dark text-light" id="stage" name="stage" required>
        <option value="">Select Stage</option>
        <option value="Slaughter">Slaughter</option>
        <option value="Processing">Processing</option>
        <option value="Storage">Storage</option>
        <option value="Handling">Handling</option>
        <option value="Transport">Transport</option>
        <option value="Rejected">Rejected</option>
        <option value="Spoiled">Spoiled</option>
    </select>
</div>

            <div class="mb-3">
                <label for="product_type" class="form-label">Product Type</label>
                <input type="text" class="form-control bg-dark text-light" id="product_type" name="product_type" required>
            </div>
            <div class="mb-3">
                <label for="quantity_lost" class="form-label">Quantity Lost (kg)</label>
                <input type="number" step="0.01" class="form-control bg-dark text-light" id="quantity_lost" name="quantity_lost" required>
            </div>
            <div class="mb-3">
                <label for="loss_reason" class="form-label">Loss Reason</label>
                <textarea class="form-control bg-dark text-light" id="loss_reason" name="loss_reason" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>


=======
>>>>>>> 3bce404d4f3224ab9ff85b4244ea11e36cc83cdc
            <section class="table-responsive p-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Facility</th>
                            <th>Stage</th>
                            <th>Product Type</th>
                            <th>Quantity Lost</th>
                            <th>Loss Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tableData)): ?>
                            <?php foreach ($tableData as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row["date_time"]) ?></td>
                                    <td><?= htmlspecialchars($row["facility"]) ?></td>
                                    <td><?= htmlspecialchars($row["stage"]) ?></td>
                                    <td><?= htmlspecialchars($row["product_type"]) ?></td>
                                    <td><?= htmlspecialchars(number_format($row["quantity_lost"], 2)) ?> kg</td>
                                    <td><?= htmlspecialchars($row["loss_reason"]) ?></td>
                                    <td>
                                        <a href='editloss.php?id=<?= htmlspecialchars($row['id']) ?>' class='btn btn-sm btn-warning me-1'><i class='bi bi-pencil'></i> Edit</a>
                                        <button type='button' class='btn btn-sm btn-danger' onclick='confirmDelete(<?= htmlspecialchars($row['id']) ?>)'><i class='bi bi-trash'></i> Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan='7'>No loss data found matching your criteria.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>


        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script>
        // Add the confirmDelete function here if it's not in dashboard.js
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this loss entry?")) {
                window.location.href = 'deleteloss.php?id=' + id; // Link to deleteloss.php
            }
        }

        // You had a search input and button in the header that wasn't part of the form.
        // We've now added a search input directly into the main filter form.
        // If you still want the header search to trigger the form submission,
        // you can use JavaScript to copy the value from the header search input
        // to the filter form's search input and then submit the form.

        document.addEventListener('DOMContentLoaded', function() {
            const headerSearchInput = document.querySelector('.header .search-input');
            const filterFormSearchInput = document.getElementById('search-term-filter');
            const headerSearchButton = document.querySelector('.header button[type="submit"]');
            const filterForm = document.getElementById('filterForm'); // Assuming you give your filter form an ID

            if (headerSearchInput && filterFormSearchInput && headerSearchButton && filterForm) {
                headerSearchButton.addEventListener('click', function(event) {
                    // Prevent the default button behavior
                    event.preventDefault();

                    // Copy the value from the header search to the form search
                    filterFormSearchInput.value = headerSearchInput.value;

                    // Submit the filter form
                    filterForm.submit();
                });

                 // Optional: Also trigger submit if the user presses Enter in the header search field
                headerSearchInput.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                         event.preventDefault(); // Prevent default form submission if input is inside a form (it isn't here, but good practice)
                         filterFormSearchInput.value = headerSearchInput.value;
                         filterForm.submit();
                    }
                });
            }
        });
    </script>
</body>
</html>