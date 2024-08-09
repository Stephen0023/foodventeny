<?php
// Database configuration
define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASSWORD', 'rootpassword');
define('DB_NAME', 'foodventeny_db');

function getDatabaseConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>
