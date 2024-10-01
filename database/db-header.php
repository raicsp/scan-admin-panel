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

// Check if firstname, lastname, position, and profile picture are set in the session
$fullname = isset($_SESSION['firstname']) && isset($_SESSION['lastname']) 
    ? htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']) 
    : 'Guest';

$position = isset($_SESSION['position']) 
    ? htmlspecialchars($_SESSION['position']) 
    : 'Unknown Position';

// Add profile picture variable
$profile_pic = isset($_SESSION['profile_pic']) 
    ? htmlspecialchars($_SESSION['profile_pic']) 
    : 'assets/img/default-profile.png';

$email= isset($_SESSION['email']) 
    ? htmlspecialchars($_SESSION['email']) 
    : '';

    $id= isset($_SESSION['user_id']) 
    ? htmlspecialchars($_SESSION['user_id']) 
    : '';
?>
