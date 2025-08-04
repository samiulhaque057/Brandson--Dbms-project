 <?php
include 'includes/config.php';
include 'includes/functions.php';



// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $type = sanitize($_POST['type']);
    $batch = sanitize($_POST['batch']);
    $quantity = (float) $_POST['quantity'];
    $supplier = sanitize($_POST['supplier']);
    $cost = (float) $_POST['cost'];
    $processingDate = sanitize($_POST['processingDate']);
    $expirationDate = sanitize($_POST['expirationDate']);    
    $location = sanitize($_POST['location']);

    
    
   
    
    // Validate required fields
    $errors = [];
    
    if (empty($type)) {
        $errors[] = "Meat type is required";
    }
    
    if (empty($batch)) {
        $errors[] = "Batch number is required";
    }
    
    if ($quantity <= 0) {
        $errors[] = "Quantity must be greater than zero";
    }
    
    if (empty($supplier)) {
        $errors[] = "Supplier is required";
    }
    
    if ($cost <= 0) {
        $errors[] = "Cost must be greater than zero";
    }
    
    if (empty($processingDate)) {
        $errors[] = "Processing date is required";
    }
    
    if (empty($expirationDate)) {
        $errors[] = "Expiration date is required";
    }
    
    if (empty($location)) {
        $errors[] = "Storage location is required";
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        /////////////// Prepare the SQL query to insert data into the stockData table////////////////
        $sql = "INSERT INTO stockData (type, batch_id, quantity, supplier, cost, processing_date, expiration_date, location) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssss", $type, $batch, $quantity, $supplier, $cost, $processingDate, $expirationDate, $location);
        $stmt->execute();
        
    // Update the used_capacity in the cold_storages table
    $update_sql = "UPDATE cold_storages SET used_capacity = used_capacity + ? WHERE location = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ds", $quantity, $location); // 'd' for double (quantity) and 's' for string (location)
    $update_stmt->execute();

    // Check if the update was successful
    if ($update_stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Stock added and used capacity updated successfully";
    } else {
        $_SESSION['error_message'] = "Failed to update used capacity";
    }

    // Close the statement
    $update_stmt->close();
    
    // Close the main statement
    $stmt->close();
}
}

// Fetch storage locations from the database
$locations = [];
$sql = "SELECT location FROM cold_storages"; 
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['location'];
}
$conn->close();
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
    <link rel="stylesheet" href="css/styles3.css">
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
                <a href="dashboard.php" class="nav-item ">
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
                <a href="#" class="nav-item active">
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
                <a href="#" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span class="nav-item-name">Settings</span>
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
                <div class="search-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" placeholder="Search inventory, batches..." class="search-input">
                </div>
                
                <h1 class="page-title"> Stock Entry</h1>
                
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

            <style>
        /* Modern Add Stock Button Styles */
        .btn-modern-add {
            position: relative;
            padding: 15px 30px;
            border: none;
            background: linear-gradient(to right, #662D91, #912D73); /* Purple Gradient */
            color: #fff;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 5px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .btn-modern-add .btn-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
        }

        .btn-modern-add .btn-glow {
            position: absolute;
            top: var(--y, 0);
            left: var(--x, 0);
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            z-index: 1;
            transition: width 0.4s ease, height 0.4s ease;
        }

        .btn-modern-add:hover {
            transform: scale(1.05);
        }

        .btn-modern-add:hover .btn-glow {
            width: 300px;
            height: 300px;
        }
    </style>
</head>
<body class="bg-dark text-light">
    <div class="container-fluid">
        <div class="row">

    
        </div>


                <!-- Form Content -->
                <div class="container py-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="card bg-dark border-secondary mb-4">
                                <div class="card-header bg-dark ">
                                    <ul class="nav nav-tabs card-header-tabs">
                                        
                                        
                                    </ul>
                                </div>
                                <div class="card-body p-4">
                                    <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" id="addStockForm">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="type" class="form-label text-white">Meat Type <span class="text-danger">*</span></label>
                                                <select class="form-select bg-dark text-white border-secondary" name="type" id="type" required>
                                                    <option value="">Select Meat Type</option>
                                                    <option value="Beef">Beef</option>
                                                    <option value="Chicken">Chicken</option>
                                                    <option value="Lamb">Lamb</option>
                                                    
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="batch" class="form-label text-white">Batch Number <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark text-white border-secondary" id="batch-prefix"></span>
                                                    <input type="text" class="form-control bg-dark text-white border-secondary" name="batch" id="batch" required>
                                                </div>
                                                <div class="form-text text-secondary">Batch number will be auto-generated based on meat type</div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="quantity" class="form-label text-white">Quantity (kg) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control bg-dark text-white border-secondary" name="quantity" id="quantity" step="0.01" min="0" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="supplier" class="form-label text-white">Supplier <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control bg-dark text-white border-secondary" name="supplier" id="supplier" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cost" class="form-label text-white">Cost per kg ($) <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark text-white border-secondary">$</span>
                                                    <input type="number" class="form-control bg-dark text-white border-secondary" name="cost" id="cost" step="0.01" min="0" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="totalCost" class="form-label text-white">Total Cost ($)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark text-white border-secondary">$</span>
                                                    <input type="text" class="form-control bg-dark text-white border-secondary" id="totalCost" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="processingDate" class="form-label text-white">Processing Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control bg-dark text-white border-secondary" name="processingDate" id="processingDate" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="expirationDate" class="form-label text-white">Expiration Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control bg-dark text-white border-secondary" name="expirationDate" id="expirationDate" required>
                                            </div>
                                        </div>

                                        <div class="row">
    <div class="col-md-6 mb-3">
        <label for="location" class="form-label text-white">Storage Location <span class="text-danger">*</span></label>
        <select class="form-select bg-dark text-white border-secondary" name="location" id="location" required>
            <option value="">Select Storage Location</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="quality" class="form-label text-white">Quality Grade</label>
        <select class="form-select bg-dark text-white border-secondary" name="quality" id="quality">
            <option value="">Select Quality Grade</option>
            <option value="Premium">Premium</option>
            <option value="Standard">Standard</option>
            <option value="Economy">Economy</option>
        </select>
    </div>

<!-- Text Area for displaying used_capacity and total_capacity -->
<div class="col-md-6 mb-3">
    <label for="used_capacity" class="form-label text-white">Used Capacity</label>
    <textarea class="form-control bg-dark text-white border-secondary" name="used_capacity" id="used_capacity" readonly></textarea>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('location').addEventListener('change', function() {
        var location = this.value;

        if (location) {
            // Make AJAX request to get the used_capacity and total_capacity based on the selected location
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_used_capacity.php?location=' + encodeURIComponent(location), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText); // Parse JSON response

                        // Check if there's an error in the response
                        if (response.error) {
                            console.error(response.error);
                            document.getElementById('used_capacity').value = 'Error: ' + response.error;
                        } else {
                            // Update the used_capacity text area with the response data
                            var used_capacity = response.used_capacity || 'Data not available';
                            var total_capacity = response.total_capacity || 'Data not available';
                            document.getElementById('used_capacity').value = used_capacity + ' / ' + total_capacity + ' kg';
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        document.getElementById('used_capacity').value = 'Invalid data received';
                    }
                } else {
                    console.error('Request failed with status: ' + xhr.status);
                    document.getElementById('used_capacity').value = 'Failed to load data';
                }
            };
            xhr.send();
        } else {
            document.getElementById('used_capacity').value = ''; // Clear if no location selected
        }
    });
});
</script>


                                           


                                            
                                        </div>

                                        

                                        

                                        <div class="d-flex gap-2">
                                           
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Add Stock Button (Placed below data entry form and above table) -->
<div class="text-center mb-4">
    <button type="submit" form="addStockForm" class="btn-modern-add">
        <div class="btn-content">
            <i class="bi bi-plus-circle-fill me-2"></i>
            <span>Add Stock</span>
        </div>
        <div class="btn-glow"></div>
    </button>
</div>


<div class="card bg-dark border-secondary mb-5">
    <div class="card-header bg-dark border-secondary">
        <h5 class="card-title mb-0 text-white">Recent Stock Additions</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-white">Date Added</th>
                        <th class="text-white">Meat Type</th>
                        <th class="text-white">Batch #</th>
                        <th class="text-white">Quantity</th>
                        <th class="text-white">Added By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    
                    /////////////// Fetch the stock data from the database///////////////////////
                    // Make sure you have a valid database connection
                    include 'includes/config.php';

                    $sql = "SELECT * FROM stockData ORDER BY date_added DESC LIMIT 10"; // Get the 10 most recent stock records
                    $result = $conn->query($sql);

                    // Check if there are any rows in the result
                    if ($result->num_rows > 0) {
                        // Fetch each row and display it
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td class='text-white'>" . $row['date_added'] . "</td>
                                    <td class='text-white'>" . $row['type'] . "</td>
                                    <td class='text-white'>" . $row['batch_id'] . "</td>
                                    <td class='text-white'>" . $row['quantity'] . " kg</td>
                                    <td class='text-white'>" . $row['supplier'] . "</td>
                                  </tr>";
                        }
                    } else {
                        // If no records, display a message
                        echo "<tr><td colspan='5' class='text-center text-white'>No stock data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

                            
                            <!-- Modern Add Stock Button -->
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation and dynamic functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addStockForm');
            const typeSelect = document.getElementById('type');
            const batchInput = document.getElementById('batch');
            const batchPrefix = document.getElementById('batch-prefix');
            const quantityInput = document.getElementById('quantity');
            const costInput = document.getElementById('cost');
            const totalCostInput = document.getElementById('totalCost');
            const processingDateInput = document.getElementById('processingDate');
            const expirationDateInput = document.getElementById('expirationDate');
            
            // Set today as the default processing date
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            processingDateInput.value = formattedDate;
            
            // Set default expiration date (30 days from today)
            const thirtyDaysLater = new Date();
            thirtyDaysLater.setDate(today.getDate() + 30);
            expirationDateInput.value = thirtyDaysLater.toISOString().split('T')[0];
            
            // Generate batch prefix based on meat type
            typeSelect.addEventListener('change', function() {
                const selectedType = this.value;
                if (selectedType) {
                    const prefix = selectedType.charAt(0).toUpperCase();
                    batchPrefix.textContent = prefix + '-';
                    
                    // Generate a random batch number
                    const randomNum = Math.floor(1000 + Math.random() * 9000);
                    batchInput.value = randomNum;
                } else {
                    batchPrefix.textContent = '';
                    batchInput.value = '';
                }
            });
            
            // Calculate total cost
            function calculateTotalCost() {
                const quantity = parseFloat(quantityInput.value) || 0;
                const cost = parseFloat(costInput.value) || 0;
                const total = quantity * cost;
                totalCostInput.value = total.toFixed(2);
            }
            
            quantityInput.addEventListener('input', calculateTotalCost);
            costInput.addEventListener('input', calculateTotalCost);
            
            // Form validation
            form.addEventListener('submit', function(event) {
                const type = typeSelect.value;
                const batch = batchInput.value;
                const quantity = quantityInput.value;
                const supplier = document.getElementById('supplier').value;
                const cost = costInput.value;
                const processingDate = processingDateInput.value;
                const expirationDate = expirationDateInput.value;
                const location = document.getElementById('location').value;
                
                let isValid = true;
                const errors = [];
                
                if (!type) {
                    errors.push('Meat type is required');
                    isValid = false;
                }
                
                if (!batch) {
                    errors.push('Batch number is required');
                    isValid = false;
                }
                
                if (!quantity || quantity <= 0) {
                    errors.push('Quantity must be greater than zero');
                    isValid = false;
                }
                
                if (!supplier) {
                    errors.push('Supplier is required');
                    isValid = false;
                }
                
                if (!cost || cost <= 0) {
                    errors.push('Cost must be greater than zero');
                    isValid = false;
                }
                
                if (!processingDate) {
                    errors.push('Processing date is required');
                    isValid = false;
                }
                
                if (!expirationDate) {
                    errors.push('Expiration date is required');
                    isValid = false;
                }
                
                if (processingDate && expirationDate && new Date(processingDate) >= new Date(expirationDate)) {
                    errors.push('Expiration date must be after processing date');
                    isValid = false;
                }
                
                if (!location) {
                    errors.push('Storage location is required');
                    isValid = false;
                }
                
                if (!isValid) {
                    event.preventDefault();
                    
                    // Create or update alert
                    let alertDiv = document.querySelector('.alert-danger');
                    if (!alertDiv) {
                        alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger';
                        const ul = document.createElement('ul');
                        ul.className = 'mb-0';
                        alertDiv.appendChild(ul);
                        form.parentNode.insertBefore(alertDiv, form);
                    }
                    
                    const ul = alertDiv.querySelector('ul');
                    ul.innerHTML = '';
                    
                    errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        ul.appendChild(li);
                    });
                    
                    // Scroll to the top of the form
                    window.scrollTo(0, alertDiv.offsetTop - 20);
                }
            });
            
            // Add hover effect to modern button
            const modernButton = document.querySelector('.btn-modern-add');
            if (modernButton) {
                modernButton.addEventListener('mousemove', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    this.style.setProperty('--x', x + 'px');
                    this.style.setProperty('--y', y + 'px');
                });
            }
        });
    </script>
    <script src="js/dashboard.js"></script>

</body>
</html>
