<?php
// Get inventory data
function getInventoryData($conn) {
    $sql = "SELECT * FROM stockData";
    $result = $conn->query($sql);
    
    $inventoryData = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $inventoryData[] = $row; // Add each row to the inventory data array
        }
    }
    
    return $inventoryData;
}

// Calculate total inventory and breakdowns
function getInventoryBreakdown($conn) {
    $inventoryData = getInventoryData($conn);
    $totalQuantity = 0;
    $categories = [
        'Beef' => 0,
        'Chicken' => 0,
        'Lamb' => 0,
        'Other' => 0
    ];
    
    // Calculate total quantity and breakdown by category
    foreach ($inventoryData as $item) {
        $totalQuantity += $item['quantity'];
        
        // Assuming 'type' column exists, modify if needed
        switch ($item['type']) {
            case 'Beef':
                $categories['Beef'] += $item['quantity'];
                break;
            case 'Chicken':
                $categories['Chicken'] += $item['quantity'];
                break;
            case 'Lamb':
                $categories['Lamb'] += $item['quantity'];
                break;
            default:
                $categories['Other'] += $item['quantity'];
                break;
        }
    }
    
    // Calculate breakdown percentages
    $breakdown = [];
    foreach ($categories as $category => $quantity) {
        $breakdown[$category] = [
            'quantity' => $quantity,
            'percentage' => ($totalQuantity > 0) ? round(($quantity / $totalQuantity) * 100, 2) : 0
        ];
    }
    
    return [
        'total' => $totalQuantity,
        'change' => 3.2, // Example: static change, adjust based on real data
        'breakdown' => $breakdown
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
function calculateTotalInventory($conn) {
    $sql = "SELECT SUM(quantity) AS total_quantity FROM stockData";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total = $row['total_quantity'];
        
        // You could dynamically calculate the change here (for now it's static)
        $change = 3.2;
        return [
            'value' => $total,
            'change' => $change
        ];
    } else {
        return [
            'value' => 0,
            'change' => 0
        ];
    }
}


// Calculate spoilage rate (total kg after expiration date)
function calculateSpoilageRate($inventoryData) {
    $totalSpoiled = 0;

    $today = new DateTime();
    foreach ($inventoryData as $item) {
        $expirationDate = new DateTime($item['expiration_Date']);
        if ($expirationDate < $today) {
            $totalSpoiled += $item['quantity'];  // Add the quantity of expired items to the total
        }
    }

    return [
        'value' => $totalSpoiled,  // Total kg of spoiled inventory
        'change' => -0.5 // Change can be calculated based on previous week's data, here it is static for demonstration
    ];
}


// Calculate expiring soon inventory (expire in the next 2 days)
function calculateExpiringSoon($inventoryData) {
    $totalExpiringSoon = 0;

    $today = new DateTime();  // Current date
    $twoDaysFromNow = new DateTime('+2 days');  // 2 days from today
    
    foreach ($inventoryData as $item) {
        // Check if expiration_date exists and is valid
        if (isset($item['expiration_Date'])) {

            $expirationDate = new DateTime($item['expiration_Date']);  // Convert expiration_date to DateTime object
            // Check if the expiration date is within the next 2 days
            if ($expirationDate > $today && $expirationDate <= $twoDaysFromNow) {
                $totalExpiringSoon += $item['quantity'];  // Add quantity of inventory that is expiring soon
            }
        }
    }
    
    return [
        'value' => $totalExpiringSoon,  // Total kg of expiring soon inventory
        'change' => 12  // Percentage change from last week (static for demonstration)
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