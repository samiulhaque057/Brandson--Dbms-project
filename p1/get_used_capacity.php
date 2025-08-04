<?php
// get_used_capacity.php

include 'includes/config.php'; // Database connection

header('Content-Type: application/json'); // Set the response header to JSON format

if (isset($_GET['location'])) {
    $location = $_GET['location'];

    // Prepare the SQL query to fetch used_capacity and total_capacity based on the location
    $stmt = $conn->prepare("SELECT used_capacity, total_capacity FROM cold_storages WHERE location = ? LIMIT 1");

    if ($stmt) {
        // Bind the parameter to the placeholder, "s" means string
        $stmt->bind_param("s", $location);

        // Execute the query
        $stmt->execute();

        // Bind the result to variables
        $stmt->bind_result($used_capacity, $total_capacity);

        // Fetch the result
        if ($stmt->fetch()) {
            // Format the used_capacity and total_capacity to two decimal places
            $used_capacity = number_format($used_capacity, 2, '.', ''); // 2 decimal places
            $total_capacity = number_format($total_capacity, 2, '.', ''); // 2 decimal places

            // Return both used_capacity and total_capacity in JSON format
            echo json_encode([
                'used_capacity' => $used_capacity,
                'total_capacity' => $total_capacity
            ]);
        } else {
            echo json_encode(['error' => 'Location not found']); // If location not found
        }

        // Close the statement
        $stmt->close();
    } else {
        // If the statement fails, return an error
        echo json_encode(['error' => 'Database query preparation failed']);
    }
} else {
    echo json_encode(['error' => 'Location parameter is missing']); // Error if no location is provided
}

// Close the database connection
$conn->close();
?>
