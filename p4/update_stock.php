<?php
include 'includes/config.php';
include 'includes/functions.php';

// Check if form is submitted for stock adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input for stock adjustment
    $item = sanitize($_POST['item']);
    $productType = sanitize($_POST['productType']);
    $quantityChange = (float) $_POST['quantityChange'];
    $adjustmentReason = sanitize($_POST['adjustmentReason']);
    $adjustmentDate = sanitize($_POST['adjustmentDate']);
    $adjustmentTime = sanitize($_POST['adjustmentTime']);
    $adjustmentType = sanitize($_POST['adjustmentType']); // 'add' or 'subtract'
    $notes = sanitize($_POST['notes']);

    // Validate required fields
    $errors = [];

    //if (empty($item)) {
        //$errors[] = "item is required";}

    if (empty($productType)) {
        $errors[] = "Product Type is required";
    }

    if ($quantityChange === 0) {
        $errors[] = "Quantity change cannot be zero";
    }

    if (empty($adjustmentReason)) {
        $errors[] = "Adjustment Reason is required";
    }

    if (empty($adjustmentDate)) {
        $errors[] = "Adjustment Date is required";
    }

    if (empty($adjustmentTime)) {
        $errors[] = "Adjustment Time is required";
    }

    if (empty($adjustmentType)) {
        $errors[] = "Adjustment Type is required";
    }

    // If no errors, insert adjustment data into the database
    if (empty($errors)) 
    {
        /////////////// Prepare the SQL query to insert adjustment data into th table////////////////
        $sql = "INSERT INTO sellerstock (date_time, item, product_type, quantity_change, adjustment_reason, adjustment_type, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $dateTime = $adjustmentDate . ' '. $adjustmentTime;
        $stmt->bind_param("sssdsss", $dateTime, $item, $productType, $quantityChange, $adjustmentReason, $adjustmentType, $notes);
        $stmt->execute();

        // Set success message
        $_SESSION['success_message'] = "Stock adjustment recorded successfully";
        // Redirect or clear form if needed
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
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
    <link rel="stylesheet" href="css/styles3.css">
</head>

<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="logo.png" alt="Brandson Logo" width="28" height="28">
                    <span class="brand-name">Brandson</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="productseller.php" class="nav-item ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                    <span class="nav-item-dashboard">Dashboard</span>
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
            <header class="header">
                <div class="search-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" placeholder="Search inventory, batches..." class="search-input">
                </div>

                <h1 class="page-title"> Stock Adjustment</h1>

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
                /* Modern Add Stock Adjustment Button Styles */
                .btn-modern-add {
                    position: relative;
                    padding: 15px 30px;
                    border: none;
                    background: linear-gradient(to right, #4CAF50, #81C784); /* Green Gradient for Add */
                    color: #fff;
                    font-size: 1.2em;
                    font-weight: bold;
                    border-radius: 5px;
                    overflow: hidden;
                    cursor: pointer;
                    transition: transform 0.3s ease;
                }

                .btn-modern-subtract {
                    position: relative;
                    padding: 15px 30px;
                    border:continue


none;
background: linear-gradient(to right, #f44336, #e57373); /* Red Gradient for Subtract */
color: #fff;
font-size: 1.2em;
font-weight: bold;
border-radius: 5px;
overflow: hidden;
cursor: pointer;
transition: transform 0.3s ease;
margin-left: 10px;
}

            .btn-modern-add .btn-content,
            .btn-modern-subtract .btn-content {
                position: relative;
                z-index: 2;
                display: flex;
                align-items: center;
            }

            .btn-modern-add .btn-glow,
            .btn-modern-subtract .btn-glow {
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

            .btn-modern-add:hover,
            .btn-modern-subtract:hover {
                transform: scale(1.05);
            }

            .btn-modern-add:hover .btn-glow,
            .btn-modern-subtract:hover .btn-glow {
                width: 300px;
                height: 300px;
            }
        </style>
    </head>
    <body class="bg-dark text-light">
        <div class="container-fluid">
            <div class="row">
            </div>

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

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['success_message'] ?>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error_message'] ?>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                        <div class="card bg-dark border-secondary mb-4">
                            <div class="card-header bg-dark ">
                                <ul class="nav nav-tabs card-header-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active text-white" href="#">Record Stock Adjustment</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" id="stockAdjustmentForm">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="item" class="form-label text-white">item <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-dark text-white border-secondary" id="Item " name="Item " required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="productType" class="form-label text-white">Product Type <span class="text-danger">*</span></label>
                                            <select class="form-select bg-dark text-white border-secondary" id="productType" name="productType" required>
                                                <option value="">Select Product Type</option>
                                                <option value="Beef">Beef</option>
                                                <option value="Pork">Pork</option>
                                                <option value="Poultry">Poultry</option>
                                                <option value="Lamb">Lamb</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="quantityChange" class="form-label text-white">Quantity Change (kg) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control bg-dark text-white border-secondary" id="quantityChange" name="quantityChange" step="0.01" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adjustmentReason" class="form-label text-white">Reason for Adjustment <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-dark text-white border-secondary" id="adjustmentReason" name="adjustmentReason" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="adjustmentDate" class="form-label text-white">Adjustment Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control bg-dark text-white border-secondary" id="adjustmentDate" name="adjustmentDate" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adjustmentTime" class="form-label text-white">Adjustment Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control bg-dark text-white border-secondary" id="adjustmentTime" name="adjustmentTime" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="adjustmentType" class="form-label text-white">Type of Adjustment <span class="text-danger">*</span></label>
                                            <select class="form-select bg-dark text-white border-secondary" id="adjustmentType" name="adjustmentType" required>
                                                <option value="">Select Adjustment Type</option>
                                                <option value="add">Add Stock</option>
                                                <option value="subtract">Subtract Stock</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="notes" class="form-label text-white">Notes (Optional)</label>
                                            <input type="text" class="form-control bg-dark text-white border-secondary" id="notes" name="notes">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn-modern-add">
                                            <div class="btn-content">
                                                <i class="bi bi-plus-circle-fill me-2"></i>
                                                <span>Add Stock</span>
                                            </div>
                                            <div class="btn-glow"></div>
                                        </button>
                                        <button type="submit" class="btn-modern-subtract">
                                            <div class="btn-content">
                                                <i class="bi bi-dash-circle-fill me-2"></i>
                                                <span>Subtract Stock</span>
                                            </div>
                                            <div class="btn-glow"></div>
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card bg-dark border-secondary mb-5">
                            <div class="card-header bg-dark border-secondary">
                                <h5 class="card-title mb-0 text-white">Recent Stock Adjustments</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-white">Date & Time</th>
                                                <th class="text-white">Item </th>
                                                <th class="text-white">Product Type</th>
                                                <th class="text-white">Quantity Change</th>
                                                <th class="text-white">Reason</th>
                                                <th class="text-white">Type</th>
                                                <th class="text-white">Notes</th>
                                                <th class="text-white">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Fetch recent stock adjustment data from the database
                                            $sqlAdjustments = "SELECT * FROM stockAdjustments ORDER BY date_time DESC LIMIT 10";
                                            $resultAdjustments = $conn->query($sqlAdjustments);

                                            if ($resultAdjustments->num_rows > 0) {
                                                while ($rowAdjustment = $resultAdjustments->fetch_assoc()) {
                                                    echo "<tr>
                                                            <td class='text-white'>" . $rowAdjustment['date_time'] . "</td>
                                                            <td class='text-white'>" . $rowAdjustment['Item '] . "</td>
                                                            <td class='text-white'>" . $rowAdjustment['product_type'] . "</td>
                                                            <td class='text-white'>" . ($rowAdjustment['quantity_change'] > 0 ? '+' : '') . $rowAdjustment['quantity_change'] . " kg</td>
                                                            <td class='text-white'>" . $rowAdjustment['adjustment_reason'] . "</td>
                                                            <td class='text-white'>" . ucfirst($rowAdjustment['adjustment_type']) . "</td>
                                                            <td class='text-white'>" . $rowAdjustment['notes'] . "</td>
                                                            <td>
                                                                <a href='edit_adjustment.php?id=" . $rowAdjustment['id'] . "' class='btn btn-sm btn-primary'><i class='bi bi-pencil-fill'></i> Edit</a>
                                                                <button type='button' class='btn btn-sm btn-danger ms-2' onclick='confirmDelete(" . $rowAdjustment['id'] . ")'><i class='bi bi-trash-fill'></i> Delete</button>
                                                            </td>
                                                        </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center text-white'>No stock adjustments recorded yet</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js](https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js)"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stockAdjustmentForm = document.getElementById('stockAdjustmentForm');
        const adjustmentDateInput = document.getElementById('adjustmentDate');
        const adjustmentTimeInput = document.getElementById('adjustmentTime');

        // Set default date to today
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        adjustmentDateInput.value = formattedDate;

        // Set default time to current time (without seconds)
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        adjustmentTimeInput.value = `${hours}:${minutes}`;

        stockAdjustmentForm.addEventListener('submit', function(event) {
            const item = document.getElementById('item').value;
            const productType = document.getElementById('productType').value;
            const quantityChange = document.getElementById('quantityChange').value;
            const adjustmentReason = document.getElementById('adjustmentReason').value;
            const adjustmentDate = document.getElementById('adjustmentDate').value;
            const adjustmentTime = document.getElementById('adjustmentTime').value;
            const adjustmentType = document.getElementById('adjustmentType').value;

            let isValid = true;
            const errors = [];

            if (!item) errors.push('item is required');
            if (!productType) errors.push('Product Type is required');
            if (!quantityChange || parseFloat(quantityChange) === 0) errors.push('Quantity change cannot be zero');
            if (!adjustmentReason) errors.push('Reason for Adjustment is required');
            if (!adjustmentDate) errors.push('Adjustment Date is required');
            if (!adjustmentTime) errors.push('Adjustment Time is required');
            if (!adjustmentType) errors.push('Type of Adjustment is required');

            if (errors.length > 0) {
                event.preventDefault();
                let alertDiv = document.querySelector('.alert-danger');
                if (!alertDiv) {
                    alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger';
                    const ul = document.createElement('ul');
                    ul.className = 'mb-0';
                    alertDiv.appendChild(ul);
                    stockAdjustmentForm.parentNode.insertBefore(alertDiv, stockAdjustmentForm);
                }
                const ul = alertDiv.querySelector('ul');
                ul.innerHTML = '';
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    ul.appendChild(li);
                });
                window.scrollTo(0, alertDiv.offsetTop - 20);
            }
        });

        const modernButtons = document.querySelectorAll('.btn-modern-add, .btn-modern-subtract');
        modernButtons.forEach(button => {
            button.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                this.style.setProperty('--x', x + 'px');
                this.style.setProperty('--y', y + 'px');
            });
        });
    });

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this stock adjustment entry?")) {
            window.location.href = 'delete_adjustment.php?id=' + id;
        }
    }
</script>
<script src="js/dashboard.js"></script>
&lt;/body>
&lt;/html>