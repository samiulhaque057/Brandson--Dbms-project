<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $data = [
        'product' => htmlspecialchars($_POST['product']),
        'batch' => htmlspecialchars($_POST['batch']),
        'quantity' => filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_FLOAT),
        'expires' => $_POST['expires'],
        'storage' => htmlspecialchars($_POST['storage']),
        'status' => htmlspecialchars($_POST['status'])
    ];

    if (updateExpiringItem($id, $data)) {
        $_SESSION['expiry_message'] = [
            'type' => 'success',
            'text' => 'Expiry record updated successfully!'
        ];
    } else {
        $_SESSION['expiry_message'] = [
            'type' => 'danger',
            'text' => 'Failed to update expiry record'
        ];
    }
    
    header("Location: dashboard.php");
    exit();
}