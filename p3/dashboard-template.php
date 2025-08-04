<?php
// Start output buffering at the very beginning of the file
ob_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_Connection.php';

// Check for success message
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Add transport data with manual sensor ID
if (isset($_POST['add_transport'])) {
    $transport_id = $_POST['transport_id'];
    $meat_type = $_POST['meat_type'];
    $meat_quantity = $_POST['meat_quantity'];
    $start_location = $_POST['start_location'];
    $end_location = $_POST['end_location'];
    $tracking_number = $_POST['tracking_number'];
    $sensor_id = $_POST['sensor_id'];
    $sensor_name = $_POST['sensor_name'];
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // First insert into sensor table
        $stmt = $conn->prepare("INSERT INTO sensor (sensor_id, sensor_name, temperature, humidity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isdd", $sensor_id, $sensor_name, $temperature, $humidity);
        $stmt->execute();
        $stmt->close();
        
        // Then insert into transport table with the sensor_id
        $stmt = $conn->prepare("INSERT INTO transport (transport_id, meat_type, meat_quantity, start_location, end_location, tracking_number, sensor_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsssi", $transport_id, $meat_type, $meat_quantity, $start_location, $end_location, $tracking_number, $sensor_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Transport data added successfully with sensor!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['success_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Edit transport data and update sensor
if (isset($_POST['edit_transport'])) {
    $transport_id = $_POST['transport_id'];
    $meat_type = $_POST['meat_type'];
    $meat_quantity = $_POST['meat_quantity'];
    $start_location = $_POST['start_location'];
    $end_location = $_POST['end_location'];
    $tracking_number = $_POST['tracking_number'];
    $sensor_id = $_POST['sensor_id'];
    $sensor_name = $_POST['sensor_name'];
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Update sensor data
        $stmt = $conn->prepare("UPDATE sensor SET sensor_name = ?, temperature = ?, humidity = ? WHERE sensor_id = ?");
        $stmt->bind_param("sddi", $sensor_name, $temperature, $humidity, $sensor_id);
        $stmt->execute();
        $stmt->close();
        
        // Update transport data
        $stmt = $conn->prepare("UPDATE transport SET meat_type = ?, meat_quantity = ?, start_location = ?, end_location = ?, tracking_number = ? WHERE transport_id = ?");
        $stmt->bind_param("sdsssi", $meat_type, $meat_quantity, $start_location, $end_location, $tracking_number, $transport_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Transport data and sensor updated successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['success_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Delete transport data
if (isset($_GET['delete_transport'])) {
    $transport_id = $_GET['delete_transport'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get the sensor_id associated with this transport
        $stmt = $conn->prepare("SELECT sensor_id FROM transport WHERE transport_id = ?");
        $stmt->bind_param("i", $transport_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $sensor_id = $row['sensor_id'];
        $stmt->close();
        
        // Delete the transport record
        $stmt = $conn->prepare("DELETE FROM transport WHERE transport_id = ?");
        $stmt->bind_param("i", $transport_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete the associated sensor
        $stmt = $conn->prepare("DELETE FROM sensor WHERE sensor_id = ?");
        $stmt->bind_param("i", $sensor_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Transport data and associated sensor deleted successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['success_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch transport data with sensor information
$transport_result = $conn->query("
    SELECT t.*, s.sensor_name, s.temperature, s.humidity 
    FROM transport t 
    LEFT JOIN sensor s ON t.sensor_id = s.sensor_id 
    ORDER BY t.transport_id DESC
");

// Fetch recent entries: selecting location, temperature, and humidity
$stmt = $pdo->query("
    SELECT cs.coldstorage_id, cs.location, cs.total_capacity, cs.used_capacity, cs.status, 
           s.temperature as current_temp, s.humidity, s.sensor_name
    FROM cold_storages cs
    LEFT JOIN sensor s ON cs.sensor_id = s.sensor_id
    ORDER BY cs.coldstorage_id ASC
");
$storageData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate arrays for labels (location), temperatures, and humidities
$labels = [];
$temperatures = [];
$humidities = [];
$storageIds = [];

foreach ($storageData as $row) {
    $labels[] = $row['location'];           // Location names for X-axis
    $temperatures[] = $row['current_temp'];  // Temperature
    $humidities[] = $row['humidity'];        // Humidity
    $storageIds[] = $row['coldstorage_id'];  // Add the coldstorage_id
}

// Convert PHP arrays to JSON for JavaScript
$labelsJson = json_encode($labels);
$temperaturesJson = json_encode($temperatures);
$humiditiesJson = json_encode($humidities);
$storageIdsJson = json_encode($storageIds);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Cold Storage Monitoring</title>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom CSS -->
<style>
    /* Root Variables */
    :root {
        --bs-purple: #a855f7;
        --bs-pink: #ec4899;
        --bs-orange: #f97316;
        --bs-blue: #3b82f6;
        --bs-gray: #6b7280;
        
        --sidebar-width: 220px;
        --sidebar-bg: #000000;
        --sidebar-hover: #1a1a1a;
        --sidebar-active: #1e1e1e;
        --header-height: 60px;
        --content-bg: #000000;
        
        --primary: #212529;
        --secondary: #343a40;
        --success: #198754;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #0dcaf0;
        --dark: #000000;
        --light: #f8f9fa;
        --body-bg: #000000;
        --card-bg: #121212;
        --text-main: #e9ecef;
        --text-muted: #adb5bd;
        --border-color: #2c2c2c;
        --table-bg: #0a0a0a;
        --table-header-bg: #000000;
        --table-border: #2c2c2c;
        --table-row-hover: #1a1a1a;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: var(--body-bg);
        color: var(--text-main);
        min-height: 100vh;
    }

    .app-container {
        display: flex;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    /* Sidebar Styles - Updated */
    .sidebar {
        width: var(--sidebar-width);
        background-color: var(--sidebar-bg);
        color: var(--text-main);
        position: fixed;
        height: 100vh;
        z-index: 1000;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-right: 1px solid var(--border-color);
    }

    .sidebar-header {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .brand-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #ffffff;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        padding: 1rem 0.5rem;
        gap: 0.25rem;
    }

    .nav-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        color: #ffffff;
        text-decoration: none;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .nav-item i {
        margin-right: 0.75rem;
        color: var(--bs-gray);
    }

    .nav-item-name, .nav-item-dashboard {
        font-size: 0.9375rem;
    }

    .nav-item:hover {
        background-color: var(--sidebar-hover);
    }

    .nav-item:hover i {
        color: #ffffff;
    }

    .nav-item.active {
        background-color: var(--sidebar-active);
    }

    .nav-item.active i {
        color: var(--bs-purple);
    }

    /* Main Content Styles */
    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        border: 2px solid var(--border-color); /* Keep the border */
        border-radius: 10px; /* Keep rounded corners */
        padding-top: 0rem;
        padding-right: 0.5rem; /* Adjust right padding */
        padding-bottom: 1.5rem;
        padding-left: 1.5rem;
        box-sizing: border-box; /* Ensure padding doesn't affect width/height */
    }

    /* Header Styles - Updated */
    .header {
        height: var(--header-height);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        background-color: var(--content-bg);
        z-index: 5;
        margin-bottom: 1.5rem;
    }

    .search-container {
        position: relative;
        width: 280px;
    }

    .search-input {
        background-color: #1a1a1a;
        border: none;
        border-radius: 6px;
        color: #ffffff;
        padding: 0.5rem 1rem 0.5rem 2.25rem;
        width: 100%;
        height: 36px;
        font-size: 0.875rem;
    }

    .search-input::placeholder {
        color: #6c757d;
    }

    .search-input:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.25);
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .page-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
        color: #ffffff;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    /* Profile Styles - Updated */
    .profile-container {
        position: relative;
    }

    .profile-button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: var(--bs-purple);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .profile-button:hover .profile-avatar {
        box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.5);
    }

    .profile-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        width: 180px;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 20;
        display: none;
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #ffffff;
        text-decoration: none;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #1a1a1a;
    }

    /* Card Styles */
    .card {
        background-color: var(--card-bg);
        border-radius: 10px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .card-header {
        background-color: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #ffffff;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Chart Styles */
    .chart-container {
        width: 100%;
        height: 400px;
        margin-bottom: 2rem;
    }

    /* Storage Card Styles */
    .storage-card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .storage-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .storage-metrics {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .storage-metrics p {
        margin: 0;
        padding: 0.5rem;
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 6px;
    }

    /* Table Styles */
    .table {
        color: var(--text-main);
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        background-color: #0a0a0a;
        margin-bottom: 0;
    }

    .table th {
        background-color: #000000;
        color: var(--text-main);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border-bottom: 1px solid #2c2c2c;
    }

    .table td {
        padding: 0.5rem; /* Reduce padding to decrease vertical spacing */
        border-bottom: 1px solid #2c2c2c; /* Set border width to 1px */
        vertical-align: middle;
        padding-left: 2rem;
    }

    .table tbody tr {
        background-color: #0a0a0a;
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #1a1a1a;
    }

    .table-responsive {
        background-color: #0a0a0a;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #2c2c2c;
    }

    /* Filter buttons */
    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .filter-btn {
        background-color: transparent;
        border: 1px solid #2c2c2c;
        color: var(--text-main);
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .filter-btn:hover, .filter-btn.active {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .filter-btn.active {
        border-color: #212529;
    }

    /* Type indicators */
    .type-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .type-beef {
        background-color: #9333ea;
    }

    .type-chicken {
        background-color: #ec4899;
    }

    .type-lamb {
        background-color: #3b82f6;
    }

    /* Button Styles */
    .btn-primary {
        background-color: #212529;
        border-color: #212529;
    }

    .btn-primary:hover {
        background-color: #343a40;
        border-color: #343a40;
    }

    .btn-danger {
        background-color: var(--danger);
        border-color: var(--danger);
    }

    .btn-add-storage {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #212529;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        margin-bottom: 1.5rem;
    }

    .btn-add-storage:hover {
        background-color: #343a40;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .btn-add-transport {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #212529;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        margin-bottom: 1.5rem;
    }

    .btn-add-transport:hover {
        background-color: #343a40;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .btn-download {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--danger);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-download:hover {
        background-color: #bb2d3b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: white;
    }

    /* Form Control Styles */
    .form-control, .form-select {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }

    .form-control:focus, .form-select:focus {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: #495057;
        box-shadow: 0 0 0 0.25rem rgba(73, 80, 87, 0.25);
        color: var(--text-main);
    }

    /* Modal Styles */
    .modal-content {
        background-color: #121212;
        color: var(--text-main);
        border: 1px solid #2c2c2c;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
        border-bottom: 1px solid #2c2c2c;
        padding: 1.25rem 1.5rem;
        background-color: #0a0a0a;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
        color: #ffffff;
    }

    .modal-body {
        padding: 1.5rem;
        background-color: #121212;
    }

    .modal-footer {
        border-top: 1px solid #2c2c2c;
        padding: 1.25rem 1.5rem;
        background-color: #0a0a0a;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .form-label {
        color: #adb5bd;
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .modal .form-control {
        background-color: #1a1a1a;
        border: 1px solid #333;
        color: #e9ecef;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .modal .form-control:focus {
        background-color: #1e1e1e;
        border-color: #495057;
        box-shadow: 0 0 0 0.25rem rgba(73, 80, 87, 0.25);
    }

    .modal .form-control::placeholder {
        color: #6c757d;
    }

    .modal .btn-secondary {
        background-color: #343a40;
        border-color: #343a40;
        color: #fff;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .modal .btn-secondary:hover {
        background-color: #23272b;
        border-color: #23272b;
        transform: translateY(-2px);
    }

    .modal .btn-primary {
        background-color: #212529;
        border-color: #212529;
        color: #fff;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .modal .btn-primary:hover {
        background-color: #343a40;
        border-color: #343a40;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Badge Styles */
    .badge.bg-success {
        background-color: var(--success) !important;
    }

    .badge.bg-warning {
        background-color: var(--warning) !important;
    }

    .badge.bg-danger {
        background-color: var(--danger) !important;
    }

    /* Action Buttons */
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .action-btn-edit {
        background-color: rgba(13, 202, 240, 0.1);
        color: var(--info);
        border: 1px solid rgba(13, 202, 240, 0.2);
    }

    .action-btn-edit:hover {
        background-color: rgba(13, 202, 240, 0.2);
    }

    .action-btn-delete {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--danger);
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    .action-btn-delete:hover {
        background-color: rgba(220, 53, 69, 0.2);
    }

    /* Actions column */
    .actions-cell {
        text-align: right;
    }

    .actions-menu {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background-color: transparent;
        border: none;
        color: var(--text-main);
        font-size: 1.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .actions-menu:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .sidebar {
            width: 80px;
            overflow: hidden;
        }

        .sidebar .brand-name,
        .sidebar .nav-item-name,
        .sidebar .nav-item-dashboard {
            display: none;
        }

        .main-content {
            margin-left: 80px;
            width: calc(100% - 80px);
        }

        .nav-item {
            justify-content: center;
            padding: 0.75rem;
        }
    }

    @media (max-width: 768px) {
        .storage-metrics {
            grid-template-columns: 1fr;
        }

        .header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .search-container {
            width: 100%;
        }
        
        .page-title {
            position: static;
            transform: none;
        }
    }
    
    /* Form section divider */
    .form-section-divider {
        border-top: 1px solid #2c2c2c;
        margin: 1.5rem 0;
        padding-top: 1rem;
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #adb5bd;
        margin-bottom: 1rem;
    }
</style>
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
                <a href="../samiul/dashboard.php" class="nav-item ">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span class="nav-item-dashboard">Dashboard</span>
                </a>
                <a href="../samiul/add_stock.php" class="nav-item">
                    <i class="bi bi-box-seam-fill"></i>
                    <span class="nav-item-name">Stock Entry</span>
                </a>
                <a href="dashboard-template.php" class="nav-item active">
                    <i class="bi bi-thermometer-half"></i>
                    <span class="nav-item-name">Cold Storage</span>
                </a>
                <a href="../jugumaya/dashboard-1.php" class="nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M10.29 3.86L3.86 10.29a2 2 0 0 0 0 2.83l6.43 6.43a2 2 0 0 0 2.83 0l6.43-6.43a2 2 0 0 0 0-2.83L13.12 3.86a2 2 0 0 0-2.83 0z" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span class="nav-item-name" style="margin-left: 8px;">Loss Auditor</span>
                </a>
                <a href="../raisa/productseller.php" class="nav-item">
                    <i class="bi bi-gear-fill"></i>
                    <span class="nav-item-name">Sales</span>
                </a>
                <a href="../saif/loss_dashboard.php" class="nav-item">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="nav-item-name">Preventive Measures</span>
                </a>
            </nav>
        </aside>

        

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" placeholder="Search cold storage..." class="search-input" id="searchInput">
                    <script>
                        document.querySelectorAll('.col-md-6.mb-4').forEach(function (card) {
                            const locationElem = card.querySelector('h5');
                            if (locationElem) {
                                const location = locationElem.textContent.trim().toLowerCase();
                                card.setAttribute('data-location', location);
                            }
                        });

                        // Add search filtering
                        document.getElementById('searchInput').addEventListener('input', function () {
                            const query = this.value.toLowerCase();
                            console.log("Search query:", query);
                            document.querySelectorAll('.col-md-6.mb-4').forEach(function (card) {
                                const location = card.getAttribute('data-location');
                                if (location && location.includes(query)) {
                                    card.style.display = ''; // Show the card
                                } else {
                                    card.style.display = 'none'; // Hide the card
                                }
                            });
                        });
                    </script>
                </div>
                
                <h1 class="page-title">Cold Storage</h1>
                
                <div class="profile-container">
                    <button id="profileButton" class="profile-button">
                        <div class="profile-avatar">JD</div>
                    </button>
                    <div id="profileDropdown" class="profile-dropdown">
                        <a href="#" class="dropdown-item">
                            <i class="bi bi-gear"></i>
                            Settings
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </header>

            <!-- Cold Storage Monitoring Section -->
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                    <h2>Cold Storage Monitoring</h2>
                    <button type="button" class="btn-add-storage" data-bs-toggle="modal" data-bs-target="#addStorageModal">
                        <i class="bi bi-plus-circle"></i>
                        Add Storage
                    </button>
                </div>
                
                <div class="row">
                    <?php foreach ($storageData as $storage): ?>
                        <div class="col-md-6 col-lg-4 mb-4" data-location="<?= strtolower(htmlspecialchars($storage['location'])) ?>">
                            <div class="storage-card p-3 h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="mb-0"><?= htmlspecialchars($storage['location']) ?></h5>
                                    <span class="badge bg-<?= $storage['status'] === 'critical' ? 'danger' : ($storage['status'] === 'warning' ? 'warning' : 'success') ?>">
                                        <?= ucfirst(htmlspecialchars($storage['status'])) ?>
                                    </span>
                                </div>
                                <div class="storage-metrics">
                                    <p><i class="bi bi-thermometer-half me-2"></i> <strong>Temp:</strong> <span class="current-temp"><?= htmlspecialchars($storage['current_temp']) ?>°C</span></p>
                                    <p><i class="bi bi-droplet-half me-2"></i> <strong>Humidity:</strong> <span class="current-humidity"><?= htmlspecialchars($storage['humidity']) ?>%</span></p>
                                    <p><i class="bi bi-box me-2"></i> <strong>Capacity:</strong> <?= htmlspecialchars($storage['used_capacity']) ?>/<?= htmlspecialchars($storage['total_capacity']) ?>kg</p>
                                    <p><i class="bi bi-cpu me-2"></i> <strong>Sensor:</strong> <?= htmlspecialchars($storage['sensor_name'] ?? 'N/A') ?></p>
                                    <p class="d-flex gap-2">
                                        <a href="edit_storage_form.php?id=<?= $storage['coldstorage_id'] ?>" class="btn btn-sm btn-primary flex-grow-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="delete_storage.php?id=<?= $storage['coldstorage_id'] ?>" class="btn btn-sm btn-danger flex-grow-1" onclick="return confirm('Are you sure you want to delete this storage?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Temperature and Humidity Charts -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Temperature and Humidity History</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="temperatureChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="humidityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Transport Data Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Transport Data</h3>
                    <button type="button" class="btn-add-transport" data-bs-toggle="modal" data-bs-target="#addTransportModal">
                        <i class="bi bi-plus-circle"></i>
                        Add Transport Data
                    </button>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $success_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Meat Type</th>
                                    <th>Quantity (kg)</th>
                                    <th>Start Location</th>
                                    <th>End Location</th>
                                    <th>Tracking Number</th>
                                    <th>Sensor ID</th>
                                    <th>Sensor Name</th>
                                    <th>Temperature</th>
                                    <th>Humidity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($transport_result && $transport_result->num_rows > 0): ?>
                                    <?php while ($row = $transport_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['transport_id']) ?></td>
                                            <td><?= htmlspecialchars($row['meat_type']) ?></td>
                                            <td><?= htmlspecialchars($row['meat_quantity']) ?></td>
                                            <td><?= htmlspecialchars($row['start_location']) ?></td>
                                            <td><?= htmlspecialchars($row['end_location']) ?></td>
                                            <td><?= htmlspecialchars($row['tracking_number']) ?></td>
                                            <td><?= htmlspecialchars($row['sensor_id']) ?></td>
                                            <td><?= htmlspecialchars($row['sensor_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($row['temperature']) ?>°C</td>
                                            <td><?= htmlspecialchars($row['humidity']) ?>%</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="action-btn action-btn-edit edit-transport-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editTransportModal"
                                                            data-id="<?= $row['transport_id'] ?>"
                                                            data-meat-type="<?= htmlspecialchars($row['meat_type']) ?>"
                                                            data-meat-quantity="<?= htmlspecialchars($row['meat_quantity']) ?>"
                                                            data-start-location="<?= htmlspecialchars($row['start_location']) ?>"
                                                            data-end-location="<?= htmlspecialchars($row['end_location']) ?>"
                                                            data-tracking-number="<?= htmlspecialchars($row['tracking_number']) ?>"
                                                            data-sensor-id="<?= htmlspecialchars($row['sensor_id']) ?>"
                                                            data-sensor-name="<?= htmlspecialchars($row['sensor_name']) ?>"
                                                            data-temperature="<?= htmlspecialchars($row['temperature']) ?>"
                                                            data-humidity="<?= htmlspecialchars($row['humidity']) ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="?delete_transport=<?= $row['transport_id'] ?>" class="action-btn action-btn-delete" onclick="return confirm('Are you sure you want to delete this transport data?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center">No transport data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Add Storage Modal -->
            <div class="modal fade" id="addStorageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="save_storage.php">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Storage</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" id="location" name="location" class="form-control" placeholder="Storage location" required>
                                </div>
                                <div class="mb-3">
                                    <label for="total_capacity" class="form-label">Total Capacity (kg)</label>
                                    <input type="number" id="total_capacity" name="total_capacity" class="form-control" placeholder="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="used_capacity" class="form-label">Used Capacity (kg)</label>
                                    <input type="number" id="used_capacity" name="used_capacity" class="form-control" placeholder="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="normal">Normal</option>
                                        <option value="warning">Warning</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                
                                <div class="form-section-divider"></div>
                                <div class="form-section-title">Sensor Data</div>
                                
                                <div class="mb-3">
                                    <label for="sensor_id" class="form-label">Sensor ID</label>
                                    <input type="number" id="sensor_id" name="sensor_id" class="form-control" placeholder="Sensor ID" required>
                                </div>
                                <div class="mb-3">
                                    <label for="sensor_name" class="form-label">Sensor Name</label>
                                    <input type="text" id="sensor_name" name="sensor_name" class="form-control" placeholder="Sensor name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="temperature" step="0.1" name="temperature" class="form-control" placeholder="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="humidity" class="form-label">Humidity (%)</label>
                                    <input type="number" id="humidity" name="humidity" class="form-control" placeholder="0" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="add_storage" class="btn btn-primary">Save Storage</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add Transport Modal -->
            <div class="modal fade" id="addTransportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Transport Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="transport_id" class="form-label">Transport ID</label>
                                    <input type="number" id="transport_id" name="transport_id" class="form-control" placeholder="Transport ID" required>
                                </div>
                                <div class="mb-3">
                                    <label for="meat_type" class="form-label">Meat Type</label>
                                    <input type="text" id="meat_type" name="meat_type" class="form-control" placeholder="Meat type" required>
                                </div>
                                <div class="mb-3">
                                    <label for="meat_quantity" class="form-label">Meat Quantity (kg)</label>
                                    <input type="number" id="meat_quantity" step="0.01" name="meat_quantity" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="mb-3">
                                    <label for="start_location" class="form-label">Start Location</label>
                                    <input type="text" id="start_location" name="start_location" class="form-control" placeholder="Start location" required>
                                </div>
                                <div class="mb-3">
                                    <label for="end_location" class="form-label">End Location</label>
                                    <input type="text" id="end_location" name="end_location" class="form-control" placeholder="End location" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tracking_number" class="form-label">Tracking Number</label>
                                    <input type="text" id="tracking_number" name="tracking_number" class="form-control" placeholder="Tracking number" required>
                                </div>
                                
                                <div class="form-section-divider"></div>
                                <div class="form-section-title">Sensor Data</div>
                                
                                <div class="mb-3">
                                    <label for="sensor_id" class="form-label">Sensor ID</label>
                                    <input type="number" id="sensor_id" name="sensor_id" class="form-control" placeholder="Sensor ID" required>
                                </div>
                                <div class="mb-3">
                                    <label for="sensor_name" class="form-label">Sensor Name</label>
                                    <input type="text" id="sensor_name" name="sensor_name" class="form-control" placeholder="Sensor name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="temperature" step="0.1" name="temperature" class="form-control" placeholder="-18.0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="humidity" class="form-label">Humidity (%)</label>
                                    <input type="number" id="humidity" name="humidity" class="form-control" placeholder="65" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="add_transport" class="btn btn-primary">Save Transport Data</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Transport Modal -->
            <div class="modal fade" id="editTransportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Transport Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="edit_transport_id" name="transport_id">
                                
                                <div class="mb-3">
                                    <label for="edit_meat_type" class="form-label">Meat Type</label>
                                    <input type="text" id="edit_meat_type" name="meat_type" class="form-control" placeholder="Meat type" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_meat_quantity" class="form-label">Meat Quantity (kg)</label>
                                    <input type="number" id="edit_meat_quantity" step="0.01" name="meat_quantity" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_start_location" class="form-label">Start Location</label>
                                    <input type="text" id="edit_start_location" name="start_location" class="form-control" placeholder="Start location" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_end_location" class="form-label">End Location</label>
                                    <input type="text" id="edit_end_location" name="end_location" class="form-control" placeholder="End location" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_tracking_number" class="form-label">Tracking Number</label>
                                    <input type="text" id="edit_tracking_number" name="tracking_number" class="form-control" placeholder="Tracking number" required>
                                </div>
                                
                                <div class="form-section-divider"></div>
                                <div class="form-section-title">Sensor Data</div>
                                
                                <div class="mb-3">
                                    <label for="edit_sensor_id" class="form-label">Sensor ID</label>
                                    <input type="number" id="edit_sensor_id" name="sensor_id" class="form-control" placeholder="Sensor ID" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_sensor_name" class="form-label">Sensor Name</label>
                                    <input type="text" id="edit_sensor_name" name="sensor_name" class="form-control" placeholder="Sensor name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_temperature" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="edit_temperature" step="0.1" name="temperature" class="form-control" placeholder="-18.0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_humidity" class="form-label">Humidity (%)</label>
                                    <input type="number" id="edit_humidity" name="humidity" class="form-control" placeholder="65" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="edit_transport" class="btn btn-primary">Update Transport Data</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            
            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            
            <script>
                // Profile Dropdown Toggle
                document.getElementById('profileButton').addEventListener('click', function() {
                    const dropdown = document.getElementById('profileDropdown');
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    const dropdown = document.getElementById('profileDropdown');
                    const profileButton = document.getElementById('profileButton');
                    
                    if (!profileButton.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.style.display = 'none';
                    }
                });

                // Temperature Chart
                const ctxTemp = document.getElementById('temperatureChart').getContext('2d');
                const temperatureChart = new Chart(ctxTemp, {
                    type: 'line',
                    data: {
                        labels: <?php echo $labelsJson; ?>,
                        datasets: [{
                            label: 'Temperature (°C)',
                            data: <?php echo $temperaturesJson; ?>,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#dc3545',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#e9ecef'
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#e9ecef',
                                borderColor: '#2c2c2c',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Cold Storage',
                                    color: '#e9ecef'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.05)'
                                },
                                ticks: {
                                    color: '#adb5bd'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Temperature (°C)',
                                    color: '#e9ecef'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.05)'
                                },
                                ticks: {
                                    color: '#adb5bd'
                                }
                            }
                        }
                    }
                });

                // Humidity Chart
                const ctxHum = document.getElementById('humidityChart').getContext('2d');
                const humidityChart = new Chart(ctxHum, {
                    type: 'line',
                    data: {
                        labels: <?php echo $labelsJson; ?>,
                        datasets: [{
                            label: 'Humidity (%)',
                            data: <?php echo $humiditiesJson; ?>,
                            borderColor: '#0dcaf0',
                            backgroundColor: 'rgba(13, 202, 240, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#0dcaf0',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#e9ecef'
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#e9ecef',
                                borderColor: '#2c2c2c',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Cold Storage',
                                    color: '#e9ecef'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.05)'
                                },
                                ticks: {
                                    color: '#adb5bd'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Humidity (%)',
                                    color: '#e9ecef'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.05)'
                                },
                                ticks: {
                                    color: '#adb5bd'
                                }
                            }
                        }
                    }
                });

                // Edit Transport Modal Data
                document.querySelectorAll('.edit-transport-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const meatType = this.getAttribute('data-meat-type');
                        const meatQuantity = this.getAttribute('data-meat-quantity');
                        const startLocation = this.getAttribute('data-start-location');
                        const endLocation = this.getAttribute('data-end-location');
                        const trackingNumber = this.getAttribute('data-tracking-number');
                        const sensorId = this.getAttribute('data-sensor-id');
                        const sensorName = this.getAttribute('data-sensor-name');
                        const temperature = this.getAttribute('data-temperature');
                        const humidity = this.getAttribute('data-humidity');
                        
                        document.getElementById('edit_transport_id').value = id;
                        document.getElementById('edit_meat_type').value = meatType;
                        document.getElementById('edit_meat_quantity').value = meatQuantity;
                        document.getElementById('edit_start_location').value = startLocation;
                        document.getElementById('edit_end_location').value = endLocation;
                        document.getElementById('edit_tracking_number').value = trackingNumber;
                        document.getElementById('edit_sensor_id').value = sensorId;
                        document.getElementById('edit_sensor_name').value = sensorName;
                        document.getElementById('edit_temperature').value = temperature;
                        document.getElementById('edit_humidity').value = humidity;
                    });
                });

                // Real-time data update
                function updateColdStorageData() {
                    fetch('dashboard-template.php?action=get_storage_data')
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(storage => {
                                const tempElement = document.querySelector(`[data-location="${storage.location.toLowerCase()}"] .current-temp`);
                                const humidityElement = document.querySelector(`[data-location="${storage.location.toLowerCase()}"] .current-humidity`);
                                
                                if (tempElement && humidityElement) {
                                    tempElement.innerHTML = `${storage.current_temp}°C`;
                                    humidityElement.innerHTML = `${storage.humidity}%`;
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching cold storage data:', error));
                }

                // Initial fetch
                updateColdStorageData();
                
                // Fetch every 10 seconds
                setInterval(updateColdStorageData, 10000);
            </script>
        </main>
    </div>
</body>
</html>
<?php
// End output buffering and flush
ob_end_flush();
?>