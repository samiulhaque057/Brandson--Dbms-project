<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_Connection.php';
// Get last 20 temperature and humidity records from cold_storages
$stmt = $pdo->query("SELECT location, current_temp, humidity, created_at FROM cold_storages ORDER BY created_at DESC LIMIT 20");
$storages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($storages);
?>  
