<?php
include 'database/db_connect.php';

// Initialize response array
$response = ['success' => false, 'error' => ''];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $studentId = $conn->real_escape_string($_POST['student_id']);
    $studentName = $conn->real_escape_string($_POST['student_name']);
    $schoolYear = $conn->real_escape_string($_POST['school_year']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $gradeLevel = $conn->real_escape_string($_POST['grade_level']);
    $section = $conn->real_escape_string($_POST['section']);
    $parentContact = $conn->real_escape_string($_POST['parent_contact']);
    $parentEmail = $conn->real_escape_string($_POST['parent_email']);

    // Prepare and execute update query
    $query = "
        UPDATE student
        SET 
            name = '$studentName',
            school_year = '$schoolYear',
            gender = '$gender',
            grade_level = '$gradeLevel',
            section = '$section',
            parent_contact = '$parentContact',
            parent_email = '$parentEmail'
        WHERE studentID = '$studentId'
    ";

    if ($conn->query($query) === TRUE) {
        $response['success'] = true;
    } else {
        $response['error'] = $conn->error;
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
