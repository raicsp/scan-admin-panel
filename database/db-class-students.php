<?php
include 'database/db_connect.php';
include 'database/db-class-management.php';

// Retrieve class_id from query parameter
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// Fetch students for the given class_id
$students = getStudentsByClassId($class_id);

// Fetch class details for the given class_id
$classDetails = getClassDetailsById($class_id);
?>
