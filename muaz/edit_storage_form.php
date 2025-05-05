<?php
// Start output buffering
ob_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_Connection.php';

$storage_id = isset($_GET['id']) ? $_GET['id'] : 0;
$storage = null;
$sensor = null;

// Fetch storage and sensor data
if ($storage_id > 0) {
    $stmt = $pdo->prepare("
        SELECT cs.*, s.sensor_name, s.temperature, s.humidity 
        FROM cold_storages cs
        LEFT JOIN sensor s ON cs.sensor_id = s.sensor_id
        WHERE cs.coldstorage_id = ?
    ");
    $stmt->execute([$storage_id]);
    $storage = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if (isset($_POST['update_storage'])) {
    $location = $_POST['location'];
    $total_capacity = $_POST['total_capacity'];
    $used_capacity = $_POST['used_capacity'];
    $status = $_POST['status'];
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
        
        // Update cold storage data
        $stmt = $conn->prepare("UPDATE cold_storages SET location = ?, total_capacity = ?, used_capacity = ?, status = ? WHERE coldstorage_id = ?");
        $stmt->bind_param("sddsi", $location, $total_capacity, $used_capacity, $status, $storage_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Cold storage updated successfully!";
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['success_message'] = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cold Storage</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bs-purple: #a855f7;
            --bs-body-bg: #000000;
            --bs-body-color: #e9ecef;
            --bs-card-bg: #121212;
            --bs-border-color: #2c2c2c;
        }
        
        body {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        
        .card {
            background-color: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color);
        }
        
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--bs-border-color);
            color: var(--bs-body-color);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--bs-body-color);
        }
        
        .btn-primary {
            background-color: #212529;
            border-color: #212529;
        }
        
        .btn-primary:hover {
            background-color: #343a40;
            border-color: #343a40;
        }
        
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
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Edit Cold Storage</h3>
                        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($storage): ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" id="location" name="location" class="form-control" value="<?= htmlspecialchars($storage['location']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="total_capacity" class="form-label">Total Capacity (kg)</label>
                                    <input type="number" id="total_capacity" name="total_capacity" class="form-control" value="<?= htmlspecialchars($storage['total_capacity']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="used_capacity" class="form-label">Used Capacity (kg)</label>
                                    <input type="number" id="used_capacity" name="used_capacity" class="form-control" value="<?= htmlspecialchars($storage['used_capacity']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="normal" <?= $storage['status'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                                        <option value="warning" <?= $storage['status'] === 'warning' ? 'selected' : '' ?>>Warning</option>
                                        <option value="critical" <?= $storage['status'] === 'critical' ? 'selected' : '' ?>>Critical</option>
                                    </select>
                                </div>
                                
                                <div class="form-section-divider"></div>
                                <div class="form-section-title">Sensor Data</div>
                                
                                <input type="hidden" name="sensor_id" value="<?= htmlspecialchars($storage['sensor_id']) ?>">
                                
                                <div class="mb-3">
                                    <label for="sensor_name" class="form-label">Sensor Name</label>
                                    <input type="text" id="sensor_name" name="sensor_name" class="form-control" value="<?= htmlspecialchars($storage['sensor_name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="temperature" step="0.1" name="temperature" class="form-control" value="<?= htmlspecialchars($storage['temperature']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="humidity" class="form-label">Humidity (%)</label>
                                    <input type="number" id="humidity" name="humidity" class="form-control" value="<?= htmlspecialchars($storage['humidity']) ?>" required>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" name="update_storage" class="btn btn-primary">Update Storage</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                Cold storage not found.
                                <a href="dashboard-template.php" class="alert-link">Return to dashboard</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// End output buffering and flush
ob_end_flush();
?>