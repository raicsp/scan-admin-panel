<?php
include 'database/db_connect.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user wants to log out
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Define variables with session data
$fullname = isset($_SESSION['firstname'], $_SESSION['lastname']) 
    ? htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']) 
    : 'Guest';

$position = isset($_SESSION['position']) 
    ? htmlspecialchars($_SESSION['position']) 
    : 'Unknown Position';

$profile_pic = isset($_SESSION['profile_pic']) 
    ? htmlspecialchars($_SESSION['profile_pic']) 
    : 'assets/img/default-profile.png';

$email = isset($_SESSION['email']) 
    ? htmlspecialchars($_SESSION['email']) 
    : '';

$id = isset($_SESSION['user_id']) 
    ? htmlspecialchars($_SESSION['user_id']) 
    : '';

// Add a role variable if available in the session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

?>
