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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/styles-dashboard2.css">
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

            <section class="filters p-3">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="start-date">Start Date</label>
                        <input type="date" class="form-control" id="start-date">
                    </div>
                    <div class="col-md-3">
                        <label for="end-date">End Date</label>
                        <input type="date" class="form-control" id="end-date">
                    </div>
                    <div class="col-md-3">
                        <label for="meat-type">Meat Type</label>
                        <select class="form-control" id="meat-type">
                            <option>All</option>
                            <option>Beef</option>
                            <option>Poultry</option>
                            <option>Pork</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="facility">Facility</label>
                        <select class="form-control" id="facility">
                            <option>All</option>
                            <option>Slaughterhouse</option>
                            <option>Storage</option>
                            <option>Distribution Center</option>
                        </select>
                    </div>
                </div>

            </section>

            <section class="dashboard-cards row p-3">
                <div class="col-md-2">
                    <div class="card text-bg-light mb-3" style="background-color: #0d6efd; color: white;">
                        <div class="card-body">
                            <h5 class="card-title">Slaughter Loss</h5>
                            <p>80 kg (5%)<br>Reason: Human Error</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-bg-light mb-3" style="background-color: #fd7e14; color: white;">
                        <div class="card-body">
                            <h5 class="card-title">Processing Loss</h5>
                            <p>50 kg (3%)<br>Reason: Machinery Fault</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-bg-light mb-3" style="background-color: #e83e8c; color: white;">
                        <div class="card-body">
                            <h5 class="card-title">Storage Loss</h5>
                            <p>60 kg (4%)<br>Reason: Spoilage</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-bg-light mb-3" style="background-color: #f8d7da; color: #842029;">
                        <div class="card-body">
                            <h5 class="card-title">Handling Loss</h5>
                            <p>30 kg (2%)<br>Reason: Mishandling</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-bg-light mb-3" style="background-color: #adb5bd; color: black;">
                        <div class="card-body">
                            <h5 class="card-title">Transport Loss</h5>
                            <p>40 kg (2.5%)<br>Reason: Temperature Failure</p>
                        </div>
                    </div>
                </div><div class="col-md-2">
                    <div class="card text-bg-light mb-3" style="background-color: #ffc107; color:black;">
                        <div class="card-body">
                            <h5 class="card-title">Rejected Stock</h5>
                            <p>20 kg (1%)<br>Reason: Contamination</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-bg-light mb-3" style="background-color: #dc3545; color: white;">
                        <div class="card-body">
                            <h5 class="card-title">Spoiled Inventory</h5>
                            <p>15 kg (0.8%)<br>Reason: Expired</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="chart-container p-3">
                <canvas id="lossBarChart" height="100"></canvas>
                <script>
                    const ctx = document.getElementById('lossBarChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Slaughter', 'Processing', 'Storage', 'Handling', 'Transport', 'Rejected', 'Spoiled'],
                            datasets: [{
                                label: 'Loss %',
                                data: [5, 3, 4, 2, 2.5, 1, 0.8],
                                backgroundColor: [
                                    '#0d6efd', '#fd7e14', '#e83e8c', '#f8d7da', '#adb5bd', '#ffc107', '#dc3545'
                                ]
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Loss Percentage'
                                    }
                                }
                            }
                        }
                    });
                </script>
            </section>

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
                        <tr>
                            <td>2025-04-20 14:32</td>
                            <td>Slaughterhouse A</td>
                            <td>Slaughter</td>
                            <td>Beef</td>
                            <td>80 kg (5%)</td>
                            <td>Spoilage</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-04-21 09:15</td>
                            <td>Processing Plant B</td>
                            <td>Processing</td>
                            <td>Pork</td>
                            <td>50 kg (3%)</td>
                            <td>Machinery Fault</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-04-22 11:00</td>
                            <td>Storage Unit C</td>
                            <td>Storage</td>
                            <td>Poultry</td>
                            <td>60 kg (4%)</td>
                            <td>Spoilage</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-04-23 15:45</td>
                            <td>Distribution Center D</td>
                            <td>Handling</td>
                            <td>Beef</td>
                            <td>30 kg (2%)</td>
                            <td>Mishandling</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-04-24 07:00</td>
                            <td>Transport Truck #12</td>
                            <td>Transport</td>
                            <td>Pork</td>
                            <td>40 kg (2.5%)</td>
                            <td>Temperature Failure</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-04-25 12:30</td>
                            <td>Quality Check E</td>
                            <td>Rejection</td>
                            <td>Poultry</td>
                            <td>20 kg (1%)</td>
                            <td>Contamination</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-04-26 18:00</td>
                            <td>Storage Unit F</td>
                            <td>Spoilage</td>
                            <td>Beef</td>
                            <td>15 kg (0.8%)</td>
                            <td>Expired</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>