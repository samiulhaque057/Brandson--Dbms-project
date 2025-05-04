<?php
require 'includes/db_Connection.php';

function getColdStorages($limit = 10) {
    global $pdo;

    try {
        // Prepare a query to fetch cold storages, with a limit on number of records
        $stmt = $pdo->prepare("SELECT * FROM cold_storages ORDER BY created_at DESC LIMIT :limit");
        
        // Bind the limit parameter to prevent SQL injection
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        
        // Execute the statement
        $stmt->execute();
        
        // Fetch all results and return as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Error handling
        echo "Error: " . $e->getMessage();
        return [];
    }
}
?>
