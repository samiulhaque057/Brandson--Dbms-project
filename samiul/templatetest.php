<?php
// Set page-specific variables
$page_title = "Page Title"; // Change this for each page
$current_page = "dashboard"; // Change this to highlight the correct sidebar item

// Include necessary files
include 'includes/config.php';
include 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?= SITE_NAME ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Base CSS -->
    <link rel="stylesheet" href="css/base.css">
    <!-- Component CSS -->
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <!-- Chart.js (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Page-specific CSS (if needed) -->
    
    
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
      

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Optional: Mobile Sidebar Toggle (only needed on pages where you want it) -->
            <?php include 'includes/mobile-sidebar-toggle.php'; ?>

            <!-- Page Content -->
            <div class="dashboard-content">
                <!-- Your page content goes here -->
                <h2>Content Title</h2>
                <p>Page content...</p>
            </div>
        </main>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Your page-specific scripts -->
    <script>
        // Your JavaScript code here
    </script>
</body>
</html>
