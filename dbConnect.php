<?php
/**
 * Simple Database Connection
 * This will ensure your site works while we fix the new system
 */

// Basic database connection
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "mateen";  

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset('utf8mb4');

// Legacy mysqli connection for backward compatibility
$mysqli = $conn;
?>



