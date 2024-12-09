<?php
include 'database/db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userPosition = trim($_SESSION['position'] ?? '');
echo "<script>console.log('User Position: " . addslashes($userPosition) . "');</script>";

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}

// Define grade conditions based on user position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
  // Allow access only to Kinder to Grade-6
  $gradeCondition = "WHERE c.grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
  // Allow access only to Grade-7 to Grade-12
  $gradeCondition = "WHERE c.grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}
// Initialize response array
$response = ['success' => false, 'error' => ''];

// Handle Edit Student
if (isset($_POST['edit_student'])) {
    // Use null coalescing operator to avoid undefined array key errors
    $studentID = $_POST['id'] ?? null;
    $srcode = $_POST['srcode'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $schoolYear = $_POST['school_year'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $gradeLevel = $_POST['grade_level'] ?? '';
    $section = $_POST['section'] ?? '';
    $parentContact = $_POST['parent_contact'] ?? '';
    $parentEmail = $_POST['parent_email'] ?? '';

    if ($studentID && $fullName && $schoolYear && $gender && $gradeLevel && $section && $parentContact && $parentEmail) {
        // Get the class_id for the selected grade level and section
        $classQuery = "SELECT class_id FROM classes WHERE grade_level = ? AND section = ?";
        $stmt = $conn->prepare($classQuery);
        $stmt->bind_param('ss', $gradeLevel, $section);
        $stmt->execute();
        $classResult = $stmt->get_result();
        $classRow = $classResult->fetch_assoc();
        $classID = $classRow['class_id'] ?? null;

        if ($classID) {
            // Update student record
            $updateQuery = "
            UPDATE student 
            SET 
                srcode = ?,
                name = ?, 
                gender = ?, 
                school_year = ?, 
                parent_contact = ?, 
                gmail = ?, 
                class_id = ? 
            WHERE studentID = ?
            ";
            
            $stmt = $conn->prepare($updateQuery);
            if ($stmt) {
                $stmt->bind_param('sssssssi',$srcode,$fullName, $gender, $schoolYear, $parentContact, $parentEmail, $classID, $studentID);
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Student details updated successfully!";
                } else {
                    $response['error'] = "Failed to update student record: " . $stmt->error;
                }
            } else {
                $response['error'] = "Error preparing statement: " . $conn->error;
            }
        } else {
            $response['error'] = "Class not found for the provided grade level and section.";
        }
    } else {
        $response['error'] = "All fields are required for editing a student.";
    }
}

// Handle Delete Student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_student'])) {
    $studentID = $_POST['id'] ?? null;

    if ($studentID) {
        $deleteQuery = "DELETE FROM student WHERE studentID = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $studentID);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Student deleted successfully!";
        } else {
            $response['error'] = "Failed to delete student record.";
        }
    } else {
        $response['error'] = "Student ID is required for deletion.";
    }
}

// Fetch student data
$query = "
    SELECT 
        s.studentID,
        s.srcode,
        s.name AS student_name,
        s.gender,
        c.grade_level,
        c.section,
        s.parent_contact,
        s.gmail AS parent_email,
        s.school_year
    FROM 
        student s
    JOIN 
        classes c ON s.class_id = c.class_id
    ORDER BY
    CASE 
        WHEN c.grade_level = 'Kinder' THEN 1
        WHEN c.grade_level = 'Grade-1' THEN 2
        WHEN c.grade_level = 'Grade-2' THEN 3
        WHEN c.grade_level = 'Grade-3' THEN 4
        WHEN c.grade_level = 'Grade-4' THEN 5
        WHEN c.grade_level = 'Grade-5' THEN 6
        WHEN c.grade_level = 'Grade-6' THEN 7
        WHEN c.grade_level = 'Grade-7' THEN 8
        WHEN c.grade_level = 'Grade-8' THEN 9
        WHEN c.grade_level = 'Grade-9' THEN 10
        WHEN c.grade_level = 'Grade-10' THEN 11
        WHEN c.grade_level = 'Grade-11' THEN 12
        WHEN c.grade_level = 'Grade-12' THEN 13
        ELSE 14
    END, 
    s.name ASC
";
$result = $conn->query($query);
$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Fetch sections grouped by grade level
$sectionQuery = "SELECT grade_level, section FROM classes";
$sectionResult = $conn->query($sectionQuery);

$gradeSections = [];

while ($row = $sectionResult->fetch_assoc()) {
    $gradeSections[$row['grade_level']][] = $row['section'];
}

// Pass the sections data to JavaScript
echo "<script>var gradeSections = " . json_encode($gradeSections) . ";</script>";
?>

