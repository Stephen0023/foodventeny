<?php
// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the routing logic
require_once './routes.php';

// Run the router function
route();
?>