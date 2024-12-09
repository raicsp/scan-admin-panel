<?php
include 'database/db_connect.php';
include 'database/db-class-management.php';
$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}

// Retrieve class_id from query parameter
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// Fetch students for the given class_id
$students = getStudentsByClassId($class_id);

// Fetch class details for the given class_id
$classDetails = getClassDetailsById($class_id);
?>
