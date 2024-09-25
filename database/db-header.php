<?php

include 'database/db_connect.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if the user wants to log out
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit();
}
// Check if firstname and lastname are set in the session
$fullname = isset($_SESSION['firstname']) && isset($_SESSION['lastname']) 
    ? htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']) 
    : 'Guest';
    
    $conn->close();
    ?>
    
