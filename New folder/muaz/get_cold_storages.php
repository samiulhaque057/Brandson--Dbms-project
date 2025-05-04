<?php
require 'includes/db_Connection.php';

function getColdStorages() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM cold_storages ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
