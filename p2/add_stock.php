<?php
include 'includes/config.php';
include 'includes/dbConnection.php'; // Include db connection directly
include 'includes/functions.php';
session_start(); // Start session for messages

$errors = []; // Initialize errors array

// Check if form is submitted for loss entry
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input for loss entry
    // Use sanitize() function from includes/functions.php
    $facility = sanitize($_POST['facility']);
    $stage = sanitize($_POST['stage']);
    $productType = sanitize($_POST['productType']);
    // Cast to float and validate
    $quantityLost = filter_var($_POST['quantityLost'], FILTER_VALIDATE_FLOAT);
    $lossReason = sanitize($_POST['lossReason']);
    $lossDate = sanitize($_POST['lossDate']);
    $lossTime = sanitize($_POST['lossTime']);
    $evidence = sanitize($_POST['evidence']);

    // Combine date and time for the database
    $dateTime = $lossDate . ' ' . $lossTime;

    // Validate required fields
    if (empty($facility)) {
        $errors[] = "Facility is required.";
    }
    if (empty($stage)) {
        $errors[] = "Stage is required.";
    }
    if (empty($productType)) {
        $errors[] = "Product Type is required.";
    }
    if ($quantityLost === false || $quantityLost <= 0) {
        $errors[] = "Quantity lost must be a positive number.";
    }
    if (empty($lossReason)) {
        $errors[] = "Loss Reason is required.";
    }
    if (empty($lossDate)) {
        $errors[] = "Loss Date is required.";
    }
    if (empty($lossTime)) {
        $errors[] = "Loss Time is required.";
    }

    // If no errors, insert loss data into the database
    if (empty($errors)) {
        // Prepare the SQL query to insert loss data into the loststock table
        // Ensure your loststock table has an auto-incrementing 'id' column
        $sql = "INSERT INTO loststock (date_time, facility, stage, product_type, quantity_lost, loss_reason, evidence)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Use prepared statement for security
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters: s=string, d=double, i=integer
            $stmt->bind_param("sssssss", $dateTime, $facility, $stage, $productType, $quantityLost, $lossReason, $evidence);

            if ($stmt->execute()) {
                // Set success message
                $_SESSION['success_message'] = "Loss recorded successfully.";
                 // Clear form fields after successful submission
                // You might redirect instead: header("Location: add_stock.php"); exit();
            } else {
                $errors[] = "Error recording loss: " . $stmt->error;
            }

            $stmt->close();
        } else {
             $errors[] = "Database error preparing statement: " . $conn->error;
        }
    }
}

// --- Fetch recent loss entries for the table at the bottom ---
$recentLosses = [];
$sqlRecent = "SELECT id, date_time, facility, stage, product_type, quantity_lost, loss_reason FROM loststock ORDER BY date_time DESC LIMIT 10";
if ($resultRecent = $conn->query($sqlRecent)) {
    while ($row = $resultRecent->fetch_assoc()) {
        $recentLosses[] = $row;
    }
     $resultRecent->free(); // Free result set
} else {
    // Handle error fetching recent losses
    $errors[] = "Error fetching recent losses: " . $conn->error;
}


$conn->close(); // Close connection at the end of the script

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loss Entry - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles3.css"> <style>
        /* Modern Add Loss Button Styles */
        .btn-modern-add {
            position: relative;
            padding: 15px 30px;
            border: none;
            background: linear-gradient(to right, #c62828, #e53935); /* Red Gradient */
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
            height: 300px;}
    </style>
</head>
<body class="bg-dark text-light">
    <div class="app-container">
        <?php // include 'includes/sidebar.php'; ?>
         <aside class="sidebar">
             <div class="sidebar-header">
                <div class="logo">
                    <img src="logo.png" alt="Brandson Logo" width="28" height="28">
                    <span class="brand-name">Brandson</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                 <a href="dashboard-1.php" class="nav-item">
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
                 <a href="add_stock.php" class="nav-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
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
            <?php // include 'includes/header.php'; ?>
            <header class="header">
                 <div class="search-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" placeholder="Search inventory, batches..." class="search-input">
                </div>

                <h1 class="page-title">Loss Entry</h1> <div class="profile-container">
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

            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <?php
                        // Display errors from form submission
                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger">';
                            echo '<ul class="mb-0">';
                            foreach ($errors as $error) {
                                echo '<li>' . htmlspecialchars($error) . '</li>';
                            }
                            echo '</ul>';
                            echo '</div>';
                        }

                        // Display success message from form submission
                        if (isset($_SESSION['success_message'])) {
                            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                            unset($_SESSION['success_message']); // Clear the message after displaying
                        }
                        ?>

                        <div class="card bg-dark border-secondary mb-4">
                            <div class="card-header bg-dark ">
                                <ul class="nav nav-tabs card-header-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active text-white" href="#">Record Loss</a>
                                    </li>
                                    </ul>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST" action="add_stock.php" id="lossEntryForm"> <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="facility" class="form-label text-white">Facility <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-dark text-white border-secondary" id="facility" name="facility" required value="<?= htmlspecialchars($_POST['facility'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="stage" class="form-label text-white">Stage <span class="text-danger">*</span></label>
                                            <select class="form-select bg-dark text-white border-secondary" id="stage" name="stage" required>
                                                <option value="">Select Stage</option>
                                                <option value="Slaughter" <?= (($_POST['stage'] ?? '') === 'Slaughter') ? 'selected' : '' ?>>Slaughter</option>
                                                <option value="Processing" <?= (($_POST['stage'] ?? '') === 'Processing') ? 'selected' : '' ?>>Processing</option>
                                                <option value="Storage" <?= (($_POST['stage'] ?? '') === 'Storage') ? 'selected' : '' ?>>Storage</option>
                                                <option value="Handling" <?= (($_POST['stage'] ?? '') === 'Handling') ? 'selected' : '' ?>>Handling</option>
                                                <option value="Transport" <?= (($_POST['stage'] ?? '') === 'Transport') ? 'selected' : '' ?>>Transport</option>
                                                <option value="Rejected" <?= (($_POST['stage'] ?? '') === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                                                <option value="Spoiled" <?= (($_POST['stage'] ?? '') === 'Spoiled') ? 'selected' : '' ?>>Spoiled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="productType" class="form-label text-white">Product Type <span class="text-danger">*</span></label>
                                            <select class="form-select bg-dark text-white border-secondary" id="productType" name="productType" required>
                                                <option value="">Select Product Type</option>
                                                <option value="Beef" <?= (($_POST['productType'] ?? '') === 'Beef') ? 'selected' : '' ?>>Beef</option>
                                                <option value="Pork" <?= (($_POST['productType'] ?? '') === 'Pork') ? 'selected' : '' ?>>Pork</option>
                                                <option value="Poultry" <?= (($_POST['productType'] ?? '') === 'Poultry') ? 'selected' : '' ?>>Poultry</option>
                                                <option value="Lamb" <?= (($_POST['productType'] ?? '') === 'Lamb') ? 'selected' : '' ?>>Lamb</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="quantityLost" class="form-label text-white">Quantity Lost (kg) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control bg-dark text-white border-secondary" id="quantityLost" name="quantityLost" step="0.01" min="0.01" required value="<?= htmlspecialchars($_POST['quantityLost'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="lossReason" class="form-label text-white">Loss Reason <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-dark text-white border-secondary" id="lossReason" name="lossReason" required value="<?= htmlspecialchars($_POST['lossReason'] ?? '') ?>">
                                        </div>
                                    </div>
                                     <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="lossDate" class="form-label text-white">Loss Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control bg-dark text-white border-secondary" id="lossDate" name="lossDate" required value="<?= htmlspecialchars($_POST['lossDate'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lossTime" class="form-label text-white">Loss Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control bg-dark text-white border-secondary" id="lossTime" name="lossTime" required value="<?= htmlspecialchars($_POST['lossTime'] ?? '') ?>">
                                        </div>
                                    </div>
                                     <div class="row mb-3">
                                        <div class="col-md-12">
                                             <label for="evidence" class="form-label text-white">Evidence</label>
                                             <input type="text" class="form-control bg-dark text-white border-secondary" id="evidence" name="evidence" value="<?= htmlspecialchars($_POST['evidence'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn-modern-add">
                                            <div class="btn-content">
                                                <i class="bi bi-x-octagon-fill me-2"></i>
                                                <span>Record Loss</span>
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
                                <h5 class="card-title mb-0 text-white">Recent Loss Entries</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-white">Date & Time</th>
                                                <th class="text-white">Facility</th>
                                                <th class="text-white">Stage</th>
                                                <th class="text-white">Product Type</th>
                                                <th class="text-white">Quantity Lost</th>
                                                <th class="text-white">Loss Reason</th>
                                                <th class="text-white">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recentLosses)): ?>
                                                <?php foreach ($recentLosses as $rowLost): ?>
                                                    <tr>
                                                        <td class='text-white'><?= htmlspecialchars($rowLost['date_time']) ?></td>
                                                        <td class='text-white'><?= htmlspecialchars($rowLost['facility']) ?></td>
                                                        <td class='text-white'><?= htmlspecialchars($rowLost['stage']) ?></td>
                                                        <td class='text-white'><?= htmlspecialchars($rowLost['product_type']) ?></td>
                                                        <td class='text-white'><?= htmlspecialchars(number_format($rowLost['quantity_lost'], 2)) ?> kg</td>
                                                        <td class='text-white'><?= htmlspecialchars($rowLost['loss_reason']) ?></td>
                                                        <td>
                                                             <a href='editloss.php?id=<?= htmlspecialchars($rowLost['id']) ?>' class='btn btn-sm btn-primary'><i class='bi bi-pencil-fill'></i> Edit</a>
                                                            <button type='button' class='btn btn-sm btn-danger ms-2' onclick='confirmDelete(<?= htmlspecialchars($rowLost['id']) ?>)'><i class='bi bi-trash-fill'></i> Delete</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan='7' class='text-center text-white'>No loss entries recorded yet</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Keep your existing JavaScript for button glow and form validation
        document.addEventListener('DOMContentLoaded', function() {
            const lossEntryForm = document.getElementById('lossEntryForm');
            const lossDateInput = document.getElementById('lossDate');
            const lossTimeInput = document.getElementById('lossTime');

            // Set default date to today if the field is empty
            if (!lossDateInput.value) {
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                lossDateInput.value = formattedDate;
            }

            // Set default time to current time (without seconds) if the field is empty
             if (!lossTimeInput.value) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                lossTimeInput.value = `${hours}:${minutes}`;
             }


            lossEntryForm.addEventListener('submit', function(event) {
                // Basic client-side validation (backend validation is also crucial)
                const facility = document.getElementById('facility').value;
                const stage = document.getElementById('stage').value;
                const productType = document.getElementById('productType').value;
                const quantityLost = document.getElementById('quantityLost').value;
                const lossReason = document.getElementById('lossReason').value;
                const lossDate = document.getElementById('lossDate').value;
                const lossTime = document.getElementById('lossTime').value;

                let isValid = true;
                const errors = [];

                if (!facility.trim()) errors.push('Facility is required.');
                if (!stage) errors.push('Stage is required.');
                if (!productType) errors.push('Product Type is required.');
                if (!quantityLost || parseFloat(quantityLost) <= 0) errors.push('Quantity lost must be a positive number.');
                if (!lossReason.trim()) errors.push('Loss Reason is required.');
                if (!lossDate) errors.push('Loss Date is required.');
                if (!lossTime) errors.push('Loss Time is required.');


                if (errors.length > 0) {
                    event.preventDefault(); // Prevent form submission
                    let alertDiv = document.querySelector('#lossEntryForm').closest('.col-lg-10').querySelector('.alert-danger');
                     // If alert doesn't exist, create and insert it before the card
                     if (!alertDiv) {
                        alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger';
                         document.querySelector('#lossEntryForm').closest('.card').parentNode.insertBefore(alertDiv, document.querySelector('#lossEntryForm').closest('.card'));
                    } else {
                         alertDiv.innerHTML = ''; // Clear previous errors
                    }

                    const ul = document.createElement('ul');
                    ul.className = 'mb-0';
                     errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        ul.appendChild(li);
                    });
                    alertDiv.appendChild(ul);
                    window.scrollTo(0, alertDiv.offsetTop - 20); // Scroll to the error message
                } else {
                     // Remove any previous error message if validation passes
                    const alertDiv = document.querySelector('#lossEntryForm').closest('.col-lg-10').querySelector('.alert-danger');
                    if (alertDiv) {
                        alertDiv.remove();
                    }
                }
            });

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

         // Function to confirm deletion
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this loss entry?")) {
                window.location.href = 'deleteloss.php?id=' + id; // Link to deleteloss.php
            }
        }
    </script>
    <script src="js/dashboard.js"></script>

</body>
</html>