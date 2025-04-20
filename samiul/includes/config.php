<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'brandson';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // In production, you would log this error instead of displaying it
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");

// Application settings
$app_name = "Agro Farm Dashboard";
$app_version = "1.0.0";
$app_timezone = "UTC";

// Set timezone
date_default_timezone_set($app_timezone);

// Define constants, checking if already defined
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(dirname(__FILE__)));
}

if (!defined('URL_ROOT')) {
    define('URL_ROOT', 'http://localhost/demo');
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', $app_name);
}

?>
