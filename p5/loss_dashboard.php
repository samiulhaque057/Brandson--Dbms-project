<?php
session_start();
include 'includes/config.php';

// Fetch Product List for Dropdown
$productResult = $conn->query("SELECT product_id, product_name FROM products");

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity_lost = $_POST['quantity_lost'];
    $loss_reason = $_POST['loss_reason'];
    $loss_date = $_POST['loss_date'];
    $action_taken = $_POST['action_taken'];
    $improvement_notes = $_POST['improvement_notes'];

    $stmt = $conn->prepare("INSERT INTO post_harvest_loss (product_id, quantity_lost, loss_reason, loss_date, action_taken, improvement_notes) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $product_id, $quantity_lost, $loss_reason, $loss_date, $action_taken, $improvement_notes);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success_message'] = "Loss entry added successfully.";
    header('Location: loss_dashboard.php');
    exit;
}

// Fetch Loss Data
$lossData = [];
$result = $conn->query("SELECT p.product_name, l.quantity_lost, l.loss_reason, l.loss_date, l.action_taken, l.improvement_notes
                        FROM post_harvest_loss l
                        JOIN products p ON l.product_id = p.product_id
                        ORDER BY l.loss_date DESC");
while ($row = $result->fetch_assoc()) {
    $lossData[] = $row;
}

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
    <title>Post-Harvest Loss Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Post-Harvest Loss Dashboard</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <form method="post" class="mt-4 mb-5">
        <div class="form-group">
            <label>Product</label>
            <select name="product_id" class="form-control" required>
                <option value="">Select Product</option>
                <?php while ($row = $productResult->fetch_assoc()): ?>
                    <option value="<?= $row['product_id'] ?>"><?= htmlspecialchars($row['product_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Quantity Lost (kg)</label>
            <input type="number" step="0.01" name="quantity_lost" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Loss Reason</label>
            <input type="text" name="loss_reason" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Loss Date</label>
            <input type="date" name="loss_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Action Taken</label>
            <input type="text" name="action_taken" class="form-control">
        </div>
        <div class="form-group">
            <label>Improvement Notes</label>
            <input type="text" name="improvement_notes" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit Loss Entry</button>
    </form>

    <h4>Loss Records</h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Product</th>
            <th>Quantity Lost</th>
            <th>Reason</th>
            <th>Date</th>
            <th>Action Taken</th>
            <th>Improvement Notes</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lossData as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= htmlspecialchars($item['quantity_lost']) ?></td>
                <td><?= htmlspecialchars($item['loss_reason']) ?></td>
                <td><?= htmlspecialchars($item['loss_date']) ?></td>
                <td><?= htmlspecialchars($item['action_taken']) ?></td>
                <td><?= htmlspecialchars($item['improvement_notes']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Monthly Loss Trend</h4>
    <canvas id="lossChart" width="400" height="150"></canvas>
</div>

<script>
    const lossData = <?php echo json_encode($lossData); ?>;
    const monthlyLoss = {};

    lossData.forEach(item => {
        const month = new Date(item.loss_date).toISOString().slice(0, 7); // "YYYY-MM"
        if (!monthlyLoss[month]) {
            monthlyLoss[month] = 0;
        }
        monthlyLoss[month] += parseFloat(item.quantity_lost);
    });

    const months = Object.keys(monthlyLoss).sort();
    const quantities = months.map(m => monthlyLoss[m]);

    const ctx = document.getElementById('lossChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Quantity Lost (kg)',
                data: quantities,
                backgroundColor: 'rgba(255,99,132,0.2)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
