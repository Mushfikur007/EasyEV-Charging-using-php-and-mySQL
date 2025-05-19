<?php
// Include database class
require_once 'config/database.php';

// Create database connection
$database = new Database();
$conn = $database->connect();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 