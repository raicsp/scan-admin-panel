<?php
include 'db_connect.php'; // Adjust the path as needed
session_start();
$alertMessage = '';
$alertType = '';


// Check the logged-in user's position
$userPosition = trim($_SESSION['position'] ?? '');

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
    // Access only Kinder to Grade-6
    $gradeCondition = "WHERE grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Access only Grade-7 to Grade-12
    $gradeCondition = "WHERE grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

// Fetch data for classes with grade conditions
$classes = [];
$grades = [];
$sectionsByGrade = [];

$sql = "SELECT class_id, grade_level, section, assigned_teacher_id FROM classes $gradeCondition";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;

        // Add unique grades
        if (!in_array($row['grade_level'], $grades)) {
            $grades[] = $row['grade_level'];
        }

        // Group sections by grade
        if (!isset($sectionsByGrade[$row['grade_level']])) {
            $sectionsByGrade[$row['grade_level']] = [];
        }
        if (!in_array($row['section'], $sectionsByGrade[$row['grade_level']])) {
            $sectionsByGrade[$row['grade_level']][] = $row['section'];
        }
    }
} else {
    echo 'No classes found for your assigned grade levels.';
}


// Fetch Teachers with IDs
$teachers = [];
$result = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) AS fullname FROM users");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

// Sort the array by fullname in ascending order
usort($teachers, function ($a, $b) {
    return strcmp($a['fullname'], $b['fullname']);
});


// Handle Teacher Assignment Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    $teacher_id = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : 0;

    if ($class_id > 0 && $teacher_id > 0) {
        // Fetch the grade level and section for the selected class
        $sql = "SELECT grade_level, section FROM classes WHERE class_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $class = $result->fetch_assoc();
        $stmt->close();

        if ($class) {
            // Update the class assignment
            $sql = "UPDATE classes SET assigned_teacher_id = ? WHERE class_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $teacher_id, $class_id);
            if ($stmt->execute()) {
                // Update the teacher's class_id in the users table
                $sql = "UPDATE users SET class_id = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ii', $class_id, $teacher_id);
                if ($stmt->execute()) {
                    // Update the teacher_id in the student table where class_id matches
                    $sql = "UPDATE student SET teacher_Id = ? WHERE class_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ii', $teacher_id, $class_id);
                    if ($stmt->execute()) {
                        echo 'success';
                        exit;
                    } else {
                        echo 'Error updating teacher in student table: ' . $conn->error;
                        exit;
                    }
                } else {
                    echo 'Error updating teacher in users table: ' . $conn->error;
                }
                $stmt->close();
            } else {
                echo 'Error updating teacher in classes table: ' . $conn->error;
            }
            $stmt->close();
        } else {
            echo 'Class not found';
        }
    } else {
        echo 'Invalid class_id or teacher_id';
    }
}
?>