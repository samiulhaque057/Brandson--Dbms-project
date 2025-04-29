<?php

include 'includes/config.php'; // Make sure this connects $conn to your DB

// Check if ID is passed
if (!isset($_GET['id'])) {
    echo "No inventory ID specified.";
    exit;
}

$id = intval($_GET['id']);

// Fetch inventory item for editing
$sql = "SELECT * FROM expiring_inventory WHERE id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "Inventory item not found.";
    exit;
}

$row = $result->fetch_assoc();

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = $_POST['product'];
    $batch = $_POST['batch'];
    $quantity = $_POST['quantity'];
    $expires = $_POST['expires'];
    $storage = $_POST['storage'];
    $status = $_POST['status'];

    $updateSql = "UPDATE expiring_inventory 
                  SET product='$product', batch='$batch', quantity='$quantity', expires='$expires', 
                      storage='$storage', status='$status' 
                  WHERE id = $id";

    if ($conn->query($updateSql) === TRUE) {
        header("Location: inventory.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
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
                <a href="analytics.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span class="nav-item-name">Analytics</span>
                </a>
                <a href="#" class="nav-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <span class="nav-item-name">Stock Entry</span>
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
                
                <h1 class="page-title"> Edit inventory</h1>
                
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





















<!DOCTYPE html>
<html>
<head>
    <title>Edit Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles3.css">
</head>
<body class="bg-dark text-white">
div class="container mt-5">
    <h2>Edit Inventory ID: <?= $row['id'] ?></h2>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="product" class="form-label">Product</label>
            <input type="text" name="product" id="product" class="form-control" value="<?= $row['product'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="batch" class="form-label">Batch</label>
            <input type="text" name="batch" id="batch" class="form-control" value="<?= $row['batch'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity (kg)</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="<?= $row['quantity'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="expires" class="form-label">Expires</label>
            <input type="date" name="expires" id="expires" class="form-control" value="<?= $row['expires'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="storage" class="form-label">Storage</label>
            <input type="text" name="storage" id="storage" class="form-control" value="<?= $row['storage'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="normal" <?= $row['status'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                <option value="urgent" <?= $row['status'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update Inventory</button>
        <a href="inventory.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
