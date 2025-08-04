<?php
require 'includes/db_Connection.php';

$id = $_POST['coldstorage_id'];
$location = $_POST['location'];
$total = $_POST['total_capacity'];
$used = $_POST['used_capacity'];
$temp = $_POST['current_temp'];
$humidity = $_POST['humidity'];

$status = ($temp > 10 || $humidity > 70) ? 'critical' : (($temp > 5 || $humidity > 60) ? 'warning' : 'normal');

$stmt = $pdo->prepare("UPDATE cold_storages SET location=?, total_capacity=?, used_capacity=?, current_temp=?, humidity=?, status=? WHERE coldstorage_id=?");
$stmt->execute([$location, $total, $used, $temp, $humidity, $status, $id]);

header("Location: dashboard-template.php");
exit;
?>
