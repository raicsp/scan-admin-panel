<?php
$servername = "localhost"; // Change this if your database server is not localhost
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "scan"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



?>
