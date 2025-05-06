<?php
// Database connection
$host = 'localhost';
$db   = 'brandson';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity_lost = $_POST['quantity_lost'];
    $loss_reason = $_POST['loss_reason'];
    $loss_date = $_POST['loss_date'];
    $action_taken = $_POST['action_taken'];
    $improvement_notes = $_POST['improvement_notes'];

    $stmt = $pdo->prepare("INSERT INTO post_harvest_loss (product_id, quantity_lost, loss_reason, loss_date, action_taken, improvement_notes) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product_id, $quantity_lost, $loss_reason, $loss_date, $action_taken, $improvement_notes]);

    $success_message = "Loss record added successfully!";
}

// Fetch products for dropdown
$products = $pdo->query("SELECT product_id, product_name FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Fetch records for table display and chart
$records = $pdo->query("SELECT p.product_name, l.quantity_lost, l.loss_reason, l.loss_date, l.action_taken, l.improvement_notes 
                        FROM post_harvest_loss l 
                        JOIN products p ON l.product_id = p.product_id 
                        ORDER BY l.loss_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post-Harvest Loss Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="container mt-4">
    <h2 class="mb-4">Post-Harvest Loss Dashboard</h2>

    <?php if (!empty($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
                <option value="">Choose product</option>
                <?php foreach ($products as $prod): ?>
                    <option value="<?= $prod['product_id'] ?>"><?= $prod['product_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Quantity Lost</label>
            <input type="number" step="0.01" name="quantity_lost" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Reason</label>
            <input type="text" name="loss_reason" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date</label>
            <input type="date" name="loss_date" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Action Taken</label>
            <input type="text" name="action_taken" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Improvement Notes</label>
            <input type="text" name="improvement_notes" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Add Loss Record</button>
        </div>
    </form>

    <h4>Recorded Losses</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity Lost</th>
                <th>Reason</th>
                <th>Date</th>
                <th>Action</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= $r['product_name'] ?></td>
                    <td><?= $r['quantity_lost'] ?></td>
                    <td><?= $r['loss_reason'] ?></td>
                    <td><?= $r['loss_date'] ?></td>
                    <td><?= $r['action_taken'] ?></td>
                    <td><?= $r['improvement_notes'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4 class="mt-5">Monthly Loss Trend</h4>
    <canvas id="lossChart"></canvas>

    <script>
        const ctx = document.getElementById('lossChart').getContext('2d');
        const monthlyData = {};

        <?php foreach ($records as $r): 
            $month = date("Y-m", strtotime($r['loss_date']));
            echo "monthlyData['$month'] = (monthlyData['$month'] ?? 0) + {$r['quantity_lost']};
";
        endforeach; ?>

        const labels = Object.keys(monthlyData);
        const data = Object.values(monthlyData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantity Lost',
                    data: data,
                    fill: false,
                    borderWidth: 2
                }]
            }
        });
    </script>
</body>
</html>
