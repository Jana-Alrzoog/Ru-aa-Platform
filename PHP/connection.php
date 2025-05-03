<?php
// Database connection settings
$host = "localhost";  // Server host (use "localhost" for local development)
$user = "root";  // Default username for XAMPP
$password = "root";  // Default password (empty for XAMPP)
$dbname = "ruaa_db";  // Database name

// Create a new database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
