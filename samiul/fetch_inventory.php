<?php
include 'includes/config.php';

// Get the search query from the request (default to empty if not set)
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query to filter based on batch number or meat type
$sql = "SELECT batch_id, type, quantity, processing_date, expiration_date, location 
        FROM stockData 
        WHERE batch_id LIKE ? OR type LIKE ? 
        ORDER BY date_added DESC";

$stmt = $conn->prepare($sql);
$searchTerm = "%$searchQuery%";  // Add wildcards for partial matching
$stmt->bind_param("ss", $searchTerm, $searchTerm); // Bind the search term to the query

$stmt->execute();
$result = $stmt->get_result();

// Check if we have any results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Assigning the class based on meat type
        $meatClass = '';
        if ($row['type'] == 'Beef') {
            $meatClass = 'purple';
        } elseif ($row['type'] == 'Chicken') {
            $meatClass = 'pink';
        } elseif ($row['type'] == 'Lamb') {
            $meatClass = 'orange';
        } elseif ($row['type'] == 'Pork') {
            $meatClass = 'blue';
        } elseif ($row['type'] == 'Fish') {
            $meatClass = 'green';
        }

        // Output the table row dynamically
        echo "<tr class='inventory-row' data-type='" . $row['type'] . "' data-batch='" . $row['batch_id'] . "'>
                <td>
                    <div class='meat-type'>
                        <span class='meat-indicator " . $meatClass . "'></span>
                        " . $row['type'] . "
                    </div>
                </td>
                <td>" . $row['batch'] . "</td>
                <td>" . $row['quantity'] . " kg</td>
                <td>" . $row['processing_date'] . "</td>
                <td>" . $row['expiration_date'] . "</td>
                <td>" . $row['location'] . "</td>
                <td>
                    <button class='row-menu-btn' onclick='openEditModal(\"" . $row['batch_id'] . "\")'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                            <circle cx='12' cy='12' r='1'></circle>
                            <circle cx='19' cy='12' r='1'></circle>
                            <circle cx='5' cy='12' r='1'></circle>
                        </svg>
                    </button>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No matching records found.</td></tr>";
}
?>
