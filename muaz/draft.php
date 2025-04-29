<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check for success message
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>
<?php
// Fetch all loss event

if (isset($_POST['add_loss'])) {
    $product = $_POST['product'];
    $batch = $_POST['batch'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    $reason = $_POST['reason'];
    $value = $_POST['value'];

    $stmt = $conn->prepare("INSERT INTO loss_events (product, batch, quantity, date, reason, value) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssd", $product, $batch, $quantity, $date, $reason, $value);
    $stmt->execute();
    $stmt->close();
}

// Fetch loss data
$result = $conn->query("SELECT * FROM loss_events ORDER BY date DESC");

// Monthly loss data for Chart.js
$monthlyLoss = [];
$monthlyResult = $conn->query("
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(value) AS total_loss
    FROM loss_events
    GROUP BY month
    ORDER BY month ASC
");

while ($row = $monthlyResult->fetch_assoc()) {
    $monthlyLoss[] = [
        'month' => $row['month'],
        'loss' => (float)$row['total_loss']
    ];
}





$query = $conn->query("SELECT * FROM cold_storages ORDER BY location ASC");

while ($row = $query->fetch_assoc()) {
    $location = $row['location'];
    $status = ucfirst($row['status']);
    $temp = $row['current_temp'] . "°C";
    $humidity = $row['humidity'] . "%";
    $storage = $row['used_capacity'] . "/" . $row['total_capacity'] . "kg";
}






if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM loss_events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: dashboard.php"); // or wherever your table is
        exit();
    } else {
        echo "Error deleting record.";
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $product = $_POST['product'];
    $batch = $_POST['batch'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    $reason = $_POST['reason'];
    $value = $_POST['value'];

    $stmt = $conn->prepare("UPDATE loss_events SET product=?, batch=?, quantity=?, date=?, reason=?, value=? WHERE id=?");
    $stmt->bind_param("ssissdi", $product, $batch, $quantity, $date, $reason, $value, $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating record.";
    }
}

function getColdStorages() {
    global $conn; // assuming $conn is your mysqli connection

    $sql = "SELECT * FROM cold_storages"; // replace with your actual table name
    $result = $conn->query($sql);

    $storages = [];
    while ($row = $result->fetch_assoc()) {
        $storages[] = $row;
    }

    return $storages;
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
     <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ6H9mM9tCU5IxzZ4Z+H86F66zj/ZyMnEKJz3j/fFj3rcinHg9Yy9kvlFllm" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/stylestest.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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




            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
               <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ6H9mM9tCU5IxzZ4Z+H86F66zj/ZyMnEKJz3j/fFj3rcinHg9Yy9kvlFllm" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

            <!-- Dashboard Content -->
            <!-- Dashboard Content -->
            <div class="container">

   
      <!-- Cold Storage Monitoring Section -->
      <div class="row mb-5">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3>Storage Conditions</h3>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#conditionsModal">
                                    <i class="bi bi-plus-circle"></i> Update Conditions
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach (getColdStorages() as $storage): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="storage-card p-3 border rounded">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h5><?= htmlspecialchars($storage['location']) ?></h5>
                                                <span class="badge bg-<?= $storage['status'] === 'critical' ? 'danger' : ($storage['status'] === 'warning' ? 'warning' : 'success') ?>">
                                                    <?= htmlspecialchars($storage['status']) ?>
                                                </span>
                                            </div>
                                            <div class="storage-metrics">
                                                <div class="metric-item">
                                                    <i class="bi bi-thermometer-snow"></i>
                                                    <?= htmlspecialchars($storage['current_temp']) ?>°C
                                                </div>
                                                <div class="metric-item">
                                                    <i class="bi bi-droplet-fill"></i>
                                                    <?= htmlspecialchars($storage['humidity']) ?>%
                                                </div>
                                                <div class="metric-item">
                                                    <i class="bi bi-boxes"></i>
                                                    <?= htmlspecialchars($storage['used_capacity']) ?>/<?= htmlspecialchars($storage['total_capacity']) ?>kg
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div> 

        <div class="row mb-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Temperature and Humidity History</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="temperatureChart"></canvas>
                        <canvas id="humidityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


                    <div class="chart-card">
    <div class="chart-header d-flex justify-content-between align-items-center">
        <h3>Loss Chart Per Month</h3>
        <button class="chart-menu-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="1"></circle>
                <circle cx="19" cy="12" r="1"></circle>
                <circle cx="5" cy="12" r="1"></circle>
            </svg>
        </button>
    </div>
    <div class="chart-body">
        <canvas id="lossChart" height="120"></canvas>
    </div>
</div>


<!-- Conditions Update Modal -->
<div class="modal fade" id="conditionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_conditions.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Update Storage Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Storage Unit</label>
                        <select name="storage_id" class="form-select" required>
                            <!-- Loop through the fetched cold storage data -->
                            <?php foreach (getColdStorages() as $storage): ?>
                                <option value="<?= $storage['id'] ?>"><?= htmlspecialchars($storage['location']) ?> - <?= $storage['current_temp'] ?>°C, <?= $storage['humidity'] ?>%, <?= $storage['used_capacity'] ?>/<?= $storage['total_capacity'] ?> kg</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Temperature (°C)</label>
                        <input type="number" step="0.1" name="temperature" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Humidity (%)</label>
                        <input type="number" step="0.1" name="humidity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Used Capacity (kg)</label>
                        <input type="number" name="used_capacity" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>





                    
<!-- Recent Loss Events Section -->
<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Recent Loss Events</h3>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLossModal">
                <i class="bi bi-plus-circle"></i> Add Loss
            </button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Quantity (kg)</th>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Value ($)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                     <?php while($row = $result->fetch_assoc()): ?>
                      <tr data-id="<?= $row['id'] ?>">

                        <td><?= htmlspecialchars($row['product']) ?></td>
                        <td><?= htmlspecialchars($row['batch']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['date'] ?></td>
                        <td><?= htmlspecialchars($row['reason']) ?></td>
                        <td><?= $row['value'] ?></td>
                        <td>
    <button class="btn btn-sm btn-outline-secondary edit-btn" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil"></i></button>
    <form method="POST" action="delete_loss.php" style="display:inline;">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <button type="submit" class="btn btn-sm btn-outline-danger" ><i class="bi bi-trash"></i></button>
    </form>
</td>

                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addLossModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Loss</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="product" class="form-control mb-2" placeholder="Product" required>
          <input type="text" name="batch" class="form-control mb-2" placeholder="Batch" required>
          <input type="number" step="0.01" name="quantity" class="form-control mb-2" placeholder="Quantity (kg)" required>
          <input type="date" name="date" class="form-control mb-2" required>
          <input type="text" name="reason" class="form-control mb-2" placeholder="Reason" required>
          <input type="number" step="0.01" name="value" class="form-control mb-2" placeholder="Value ($)" required>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_loss" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

                <!-- Expiring Soon Section -->

                <?php
include 'includes/config.php'; // Make sure this file connects $conn to your DB
?>

<!-- Show Table Below the Form -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <h4 class="text-white">Recently Added Expiring Inventory</h4>
    <a href="add_inventory.php" class="btn btn-primary">+ Add Inventory</a>
</div>

<div class="table-responsive mt-2">
    <table class="table table-bordered text-white">
    <thead class="table-light text-dark">
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Batch</th>
        <th>Quantity</th>
        <th>Expires</th>
        <th>Storage</th>
        <th>Status</th>
        <th>Date Added</th>
        <th>Action</th> <!-- 👈 Added -->
    </tr>
</thead>

<tbody>
    <?php
    // SQL to get the 10 most recent records from expiring_inventory
    $sql = "SELECT * FROM expiring_inventory ORDER BY expires DESC LIMIT 10";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['product']}</td>
                    <td>{$row['batch']}</td>
                    <td>{$row['quantity']} kg</td>
                    <td>{$row['expires']}</td>
                    <td>{$row['storage']}</td>
                    <td><span class='badge bg-" . ($row['status'] === 'urgent' ? 'danger' : 'warning') . "'>{$row['status']}</span></td>
                    <td>{$row['created_at']}</td>
                   <td>
    <a href='edit_inventory.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
    <a href='delete_inventory.php?id={$row['id']}' class='btn btn-sm btn-danger');\">Delete</a>
</td>
                   
                </tr>";
        } // ✅ Closed while loop here
    } else {
        echo "<tr><td colspan='9' class='text-center'>No inventory records found.</td></tr>";
        // ✅ Note: colspan='9' to match the number of columns
    }
    ?>
</tbody>

    </table>
</div>




<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="edit_loss.php" id="editForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Loss Entry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit-id">
          <div class="mb-2"><input type="text" class="form-control" name="product" id="edit-product" placeholder="Product" required></div>
          <div class="mb-2"><input type="text" class="form-control" name="batch" id="edit-batch" placeholder="Batch" required></div>
          <div class="mb-2"><input type="number" class="form-control" name="quantity" id="edit-quantity" placeholder="Quantity" required></div>
          <div class="mb-2"><input type="date" class="form-control" name="date" id="edit-date" required></div>
          <div class="mb-2"><input type="text" class="form-control" name="reason" id="edit-reason" placeholder="Reason" required></div>
          <div class="mb-2"><input type="number" class="form-control" name="value" id="edit-value" placeholder="Value" required></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>



<script>
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function () {
      const row = this.closest('tr');
      document.getElementById('edit-id').value = this.dataset.id;
      document.getElementById('edit-product').value = row.children[0].textContent.trim();
      document.getElementById('edit-batch').value = row.children[1].textContent.trim();
      document.getElementById('edit-quantity').value = row.children[2].textContent.trim();
      document.getElementById('edit-date').value = row.children[3].textContent.trim();
      document.getElementById('edit-reason').value = row.children[4].textContent.trim();
      document.getElementById('edit-value').value = row.children[5].textContent.trim();
    });
  });
</script>
<script>
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function () {
      const row = this.closest('tr');
      document.getElementById('edit-id').value = this.dataset.id;
      document.getElementById('edit-product').value = row.children[0].textContent.trim();
      document.getElementById('edit-batch').value = row.children[1].textContent.trim();
      document.getElementById('edit-quantity').value = row.children[2].textContent.trim();
      document.getElementById('edit-date').value = row.children[3].textContent.trim();
      document.getElementById('edit-reason').value = row.children[4].textContent.trim();
      document.getElementById('edit-value').value = row.children[5].textContent.trim();
    });
  });
</script>









<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('lossChart').getContext('2d');
const lossChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($monthlyLoss, 'month')) ?>,
        datasets: [{
            label: 'Total Loss ($)',
            data: <?= json_encode(array_column($monthlyLoss, 'loss')) ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.7)',
            borderRadius: 5,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Loss ($)' }
            },
            x: {
                title: { display: true, text: 'Month' }
            }
        }
    }
});
</script>
<script>
    // Dummy historical data for chart (replace with DB fetch later)
    const tempData = {
        labels: ['00:00', '06:00', '12:00', '18:00', '00:00'],
        datasets: [{
            label: 'Temperature (°C)',
            data: [6.5, 7.1, 8.3, 10.2, 9.0],
            backgroundColor: 'rgba(255,99,132,0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 2,
            fill: true
        }]
    };

    const humidityData = {
        labels: ['00:00', '06:00', '12:00', '18:00', '00:00'],
        datasets: [{
            label: 'Humidity (%)',
            data: [70, 75, 72, 80, 78],
            backgroundColor: 'rgba(54,162,235,0.2)',
            borderColor: 'rgba(54,162,235,1)',
            borderWidth: 2,
            fill: true
        }]
    };

    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
    const humidityCtx = document.getElementById('humidityChart').getContext('2d');

    new Chart(tempCtx, {
        type: 'line',
        data: tempData,
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });

    new Chart(humidityCtx, {
        type: 'line',
        data: humidityData,
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });



</body>
</html>



