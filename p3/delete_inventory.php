<?php
include 'includes/config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM expiring_inventory WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to previous page
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "No ID provided.";
}
?>
