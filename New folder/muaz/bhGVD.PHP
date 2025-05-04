<?php
session_start();
include 'includes/config.php';
include 'includes/functions.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize errors array
    $errors = [];

    // Check and sanitize inputs
    $product = !empty($_POST['product']) ? sanitize($_POST['product']) : '';
    $batch = !empty($_POST['batch']) ? sanitize($_POST['batch']) : '';
    $quantity = (!empty($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0) ? (float) $_POST['quantity'] : '';
    $expires = !empty($_POST['expires']) ? sanitize($_POST['expires']) : '';
    $storage = !empty($_POST['storage']) ? sanitize($_POST['storage']) : '';

    // Validate required fields
    if (empty($product)) $errors[] = 'Product is required.';
    if (empty($batch)) $errors[] = 'Batch is required.';
    if (empty($quantity)) $errors[] = 'Valid quantity is required.';
    if (empty($expires)) $errors[] = 'Expiration date is required.';
    if (empty($storage)) $errors[] = 'Storage location is required.';

    // Calculate status based on expiration
    if (empty($errors)) {
        try {
            $expires_datetime = new DateTime($expires);
            $now = new DateTime();
            $interval = $now->diff($expires_datetime);
            $days_left = (int)$interval->format('%r%a');

            if ($days_left <= 1) {
                $status = 'urgent';
            } elseif ($days_left <= 3) { // Adjusted to 3 days for more realistic warning
                $status = 'warning';
            } else {
                $status = 'normal';
            }
        } catch (Exception $e) {
            $errors[] = 'Invalid expiration date.';
        }
    }

    // Handle errors or proceed with insertion
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
    } else {
        $sql = "INSERT INTO expiring_inventory (product, batch, quantity, expires, storage, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsss", $product, $batch, $quantity, $expires, $storage, $status);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Item added successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Existing head content remains the same -->


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
        <!-- Existing sidebar remains the same -->
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

        <main class="main-content">
            <!-- Header remains the same -->
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


            <!-- Messages Section -->
            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach($_SESSION['errors'] as $error): ?>
                        <?= $error ?><br>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <!-- Form Section -->
            <div class="container mt-5">
                <form method="POST" class="p-4 border rounded shadow-sm bg-light">
                    <div class="form-group mb-3">
                        <label class="form-label">Product</label>
                        <select name="product" class="form-control" required>
                            <?php $selected = $_SESSION['form_data']['product'] ?? ''; ?>
                            <option value="Beef" <?= $selected == 'Beef' ? 'selected' : '' ?>>Beef</option>
                            <option value="Fish" <?= $selected == 'Fish' ? 'selected' : '' ?>>Fish</option>
                            <option value="Chicken" <?= $selected == 'Chicken' ? 'selected' : '' ?>>Chicken</option>
                            <option value="Pork" <?= $selected == 'Pork' ? 'selected' : '' ?>>Pork</option>
                            <option value="Lamb" <?= $selected == 'Lamb' ? 'selected' : '' ?>>Lamb</option>
                        </select>
                    </div>

                    <!-- Other form fields with preserved values -->
                    <div class="form-group mb-3">
                        <label class="form-label">Batch</label>
                        <input name="batch" class="form-control" required 
                               value="<?= htmlspecialchars($_SESSION['form_data']['batch'] ?? '') ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Quantity (kg)</label>
                        <input name="quantity" type="number" step="0.01" class="form-control" required
                               value="<?= htmlspecialchars($_SESSION['form_data']['quantity'] ?? '') ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Expiration</label>
                        <input name="expires" type="datetime-local" class="form-control" required
                               value="<?= htmlspecialchars($_SESSION['form_data']['expires'] ?? '') ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Storage</label>
                        <select name="storage" class="form-control" required>
                            <?php $storage = $_SESSION['form_data']['storage'] ?? ''; ?>
                            <option value="">Select Storage</option>
                            <option value="Cold Storage A" <?= $storage == 'Cold Storage A' ? 'selected' : '' ?>>Cold Storage A</option>
                            <option value="Cold Storage B" <?= $storage == 'Cold Storage B' ? 'selected' : '' ?>>Cold Storage B</option>
                            <option value="Cold Storage C" <?= $storage == 'Cold Storage C' ? 'selected' : '' ?>>Cold Storage C</option>
                            <option value="Freeze 1" <?= $storage == 'Freeze 1' ? 'selected' : '' ?>>Freeze 1</option>
                            <option value="Freeze 2" <?= $storage == 'Freeze 2' ? 'selected' : '' ?>>Freeze 2</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Item</button>
                </form>
            </div>

            <!-- Table Section -->
            <div class="container mt-4">
                <h4 class="text-white mb-3">Recently Added Inventory</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Batch</th>
                                <th>Quantity</th>
                                <th>Expires</th>
                                <th>Storage</th>
                                <th>Status</th>
                                <th>Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM expiring_inventory ORDER BY id DESC LIMIT 10";
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
                                            <td><span class='badge " . match($row['status']) {
                                                'urgent' => 'text-bg-danger',
                                                'warning' => 'text-bg-warning',
                                                default => 'text-bg-success'
                                            } . "'>{$row['status']}</span></td>
                                            <td>{$row['created_at']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Profile dropdown script
        document.getElementById('profileButton').addEventListener('click', function() {
            document.getElementById('profileDropdown').classList.toggle('show');
        });
    </script>
</body>
</html>
<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>