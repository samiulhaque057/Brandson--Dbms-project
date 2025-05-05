<?php
// Include your database connection
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the JSON data sent via AJAX
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if the data is valid
    if (isset($data['batch']) && isset($data['quantity']) && isset($data['processing_date']) && isset($data['expiration_date']) && isset($data['location'])) {
        $batch = $data['batch'];
        $quantity = $data['quantity'];
        $processingDate = $data['processing_date'];
        $expirationDate = $data['expiration_date'];
        $location = $data['location'];

        // Update query
        $sql = "UPDATE stockData SET quantity = ?, processing_date = ?, expiration_date = ?, location = ? WHERE batch_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $quantity, $processingDate, $expirationDate, $location, $batch);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            // Prepare the updated data to return in the response
            $response = [
                'success' => true,
                'updatedData' => [
                    'quantity' => $quantity,
                    'processing_date' => $processingDate,
                    'expiration_date' => $expirationDate,
                    'location' => $location
                ]
            ];
        } else {
            // If the update failed
            $response = ['success' => false];
        }

        // Return the response as JSON
        echo json_encode($response);
    } else {
        // Invalid data
        echo json_encode(['success' => false]);
    }
}
?>
