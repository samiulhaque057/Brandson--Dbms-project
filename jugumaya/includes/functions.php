<?php
// Get inventory data
function getInventoryData() {
    global $conn;
    
    // In a real application, this would fetch data from the database
    // For demonstration purposes, we'll use static data
    
    return [
        [
            'id' => 1,
            'type' => 'Beef',
            'batch' => 'B-1234',
            'quantity' => 450,
            'processingDate' => '2023-04-15',
            'expirationDate' => '2023-05-15',
            'location' => 'Cold Storage A',
        ],
        [
            'id' => 2,
            'type' => 'Chicken',
            'batch' => 'C-5678',
            'quantity' => 320,
            'processingDate' => '2023-04-16',
            'expirationDate' => '2023-05-01',
            'location' => 'Cold Storage B',
        ],
        [
            'id' => 3,
            'type' => 'Lamb',
            'batch' => 'L-9012',
            'quantity' => 180,
            'processingDate' => '2023-04-14',
            'expirationDate' => '2023-05-10',
            'location' => 'Cold Storage A',
        ],
        [
            'id' => 4,
            'type' => 'Beef',
            'batch' => 'B-3456',
            'quantity' => 520,
            'processingDate' => '2023-04-12',
            'expirationDate' => '2023-05-12',
            'location' => 'Cold Storage C',
        ],
        [
            'id' => 5,
            'type' => 'Chicken',
            'batch' => 'C-7890',
            'quantity' => 280,
            'processingDate' => '2023-04-17',
            'expirationDate' => '2023-05-02',
            'location' => 'Cold Storage B',
        ],
    ];
}

// Get loss data
function getLossData() {
    global $conn;
    
    // In a real application, this would fetch data from the database
    // For demonstration purposes, we'll use static data
    
    return [
        [
            'id' => 1,
            'type' => 'Slaughter Loss',
            'batch' => 'B-1234',
            'date' => '2023-04-15',
            'lossPercentage' => 2.3,
        ],
        [
            'id' => 2,
            'type' => 'Processing Loss',
            'batch' => 'C-5678',
            'date' => '2023-04-16',
            'lossPercentage' => 3.1,
        ],
        [
            'id' => 3,
            'type' => 'Handling Loss',
            'batch' => 'L-9012',
            'date' => '2023-04-14',
            'lossPercentage' => 1.5,
        ],
        [
            'id' => 4,
            'type' => 'Spoilage Loss',
            'batch' => 'B-3456',
            'date' => '2023-04-12',
            'lossPercentage' => 2.8,
        ],
    ];
}

// Get activity data
function getActivityData() {
    global $conn;
    
    // In a real application, this would fetch data from the database
    // For demonstration purposes, we'll use static data
    
    return [
        [
            'id' => 1,
            'type' => 'add',
            'description' => 'Added 450kg of Beef (B-1234)',
            'time' => '2 hours ago',
        ],
        [
            'id' => 2,
            'type' => 'remove',
            'description' => 'Shipped 120kg of Chicken (C-5678)',
            'time' => '4 hours ago',
        ],
        [
            'id' => 3,
            'type' => 'check',
            'description' => 'Quality check on Lamb (L-9012)',
            'time' => '6 hours ago',
        ],
        [
            'id' => 4,
            'type' => 'add',
            'description' => 'Added 280kg of Chicken (C-7890)',
            'time' => '8 hours ago',
        ],
    ];
}

// Calculate total inventory
function calculateTotalInventory($inventoryData) {
    $total = 0;
    foreach ($inventoryData as $item) {
        $total += $item['quantity'];
    }
    
    // In a real application, you would compare with previous week's data
    // For demonstration, we'll use a static value
    return [
        'value' => $total,
        'change' => 3.2 // Percentage change from last week
    ];
}

// Calculate spoilage rate
function calculateSpoilageRate($lossData) {
    // In a real application, this would be calculated based on actual data
    // For demonstration, we'll use static values
    return [
        'value' => 2.4,
        'change' => -0.5 // Negative means improvement (less spoilage)
    ];
}

// Calculate expiring soon inventory
function calculateExpiringSoon($inventoryData) {
    $total = 0;
    $today = new DateTime();
    $oneWeek = new DateTime('+7 days');
    
    foreach ($inventoryData as $item) {
        $expirationDate = new DateTime($item['expirationDate']);
        if ($expirationDate > $today && $expirationDate <= $oneWeek) {
            $total += $item['quantity'];
        }
    }
    
    // In a real application, you would compare with previous week's data
    return [
        'value' => 320, // For demonstration
        'change' => 12  // Percentage change from last week
    ];
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format number with commas
function formatNumber($number) {
    return number_format($number);
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect to another page
function redirect($location) {
    header("Location: " . URL_ROOT . '/' . $location);
    exit();
}


function dashboard_table(){

    $sql = "SELECT * FROM stockData ORDER BY date_added DESC "; // Get the 10 most recent stock records
    $result = $conn->query($sql);

    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Fetch all the results into an associative array
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row; // Add each row to the data array
        }

        // Return the data array
        return $data;
    } else {
        // If no data, return an empty array
        return [];
    }

}