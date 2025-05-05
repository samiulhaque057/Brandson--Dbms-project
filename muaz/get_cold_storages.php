<?php
function getColdStorages() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT cs.coldstorage_id, cs.location, cs.total_capacity, cs.used_capacity, cs.status, 
                   s.temperature as current_temp, s.humidity, s.sensor_name, s.sensor_id
            FROM cold_storages cs
            LEFT JOIN sensor s ON cs.sensor_id = s.sensor_id
            ORDER BY cs.coldstorage_id ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log error or handle it appropriately
        return [];
    }
}
?>