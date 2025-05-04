<?php
require 'includes/db_Connection.php';

$location = $_POST['location'];
$total = $_POST['total_capacity'];
$used = $_POST['used_capacity'];
$temp = $_POST['current_temp'];
$humidity = $_POST['humidity'];

$status = ($temp > 10 || $humidity > 70) ? 'critical' : (($temp > 5 || $humidity > 60) ? 'warning' : 'normal');

$stmt = $pdo->prepare("INSERT INTO cold_storages (location, total_capacity, used_capacity, current_temp, humidity, status, created_at)
VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->execute([$location, $total, $used, $temp, $humidity, $status]);

header("Location: dashboard-template.php");
exit;
?>
