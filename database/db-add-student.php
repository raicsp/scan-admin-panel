<?php
include 'db_connect.php'; 
// Initialize arrays for grades and sections
$grades = []; 
$sections = []; 
$alertMessage = '';
$alertType = '';

// Fetch grades
$query = "SELECT DISTINCT grade_level FROM classes ORDER BY grade_level";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $grades[] = $row['grade_level'];
}

// Fetch sections for each grade
$query = "SELECT grade_level, section FROM classes ORDER BY grade_level, section";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $sections[$row['grade_level']][] = $row['section'];
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get values from POST request
    $gradeLevel = isset($_POST['grade_level']) ? $_POST['grade_level'] : null;
    $section = isset($_POST['section']) ? $_POST['section'] : null;

    if ($gradeLevel && $section) {
        // Get class_id and assigned_teacher_id based on grade level and section
        $query = "SELECT class_id, assigned_teacher_id FROM classes WHERE grade_level = ? AND section = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $gradeLevel, $section);
        $stmt->execute();
        $result = $stmt->get_result();
        $class = $result->fetch_assoc();
        
        // Check if class exists
        if ($class) {
            $classId = $class['class_id'];
            $teacherId = $class['assigned_teacher_id']; // Get the assigned teacher ID
        
            // Get student information from POST request
            $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
            $studentName = isset($_POST['student_name']) ? $_POST['student_name'] : null;
            $parentContact = isset($_POST['parent_contact']) ? $_POST['parent_contact'] : null;
            $parentEmail = isset($_POST['parent_email']) ? $_POST['parent_email'] : null;
            $schoolYear = isset($_POST['school_year']) ? $_POST['school_year'] : null;
        
            // Validate student information
            if ($studentName && $parentContact && $parentEmail && $schoolYear) {
                // Insert student record into class_list
                $insertQuery = "INSERT INTO student (gender, name, parent_contact, gmail, school_year, class_id, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                
                // Bind parameters, setting teacher_id to NULL if it's not set
                $insertStmt->bind_param('ssssssi', $gender, $studentName, $parentContact, $parentEmail, $schoolYear, $classId, $teacherId);
                $insertStmt->execute();
        
                // Check if the insertion was successful
                if ($insertStmt->affected_rows > 0) {
                    $alertMessage = 'Student added successfully!';
                    $alertType = 'success'; // Can be 'success', 'error', or 'warning'
                } else {
                    $alertMessage = 'Error adding student.';
                    $alertType = 'error';
                }
            } else {
                $alertMessage = 'Please fill in all required student information.';
                $alertType = 'warning';
            }
        } else {
            $alertMessage = 'Class not found for the selected grade and section.';
            $alertType = 'error';
        }
    } else {
        $alertMessage = 'Please select a grade level and section.';
        $alertType = 'warning';
    }
}
?>
