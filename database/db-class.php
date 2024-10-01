<?php
include 'db_connect.php'; // Adjust the path as needed

$alertMessage = '';
$alertType = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $alertMessage = 'The teacher has been assigned successfully.';
        $alertType = 'success';
    } elseif ($_GET['status'] == 'error') {
        $alertMessage = 'An error occurred. Please try again.';
        $alertType = 'danger';
    }
}

// Fetch data for classes
$classes = [];
$grades = [];
$sectionsByGrade = []; // Array to store sections by grade

// Fetch classes
$result = $conn->query("SELECT class_id, grade_level, section, assigned_teacher_id FROM classes");
if ($result->num_rows > 0) {
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
}

// Fetch Teachers with IDs
$teachers = [];
$result = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) AS fullname FROM users");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

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
