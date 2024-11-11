<?php
$alertMessage = '';
$alertType = '';

// Handle form submission for adding and updating classes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['class_id'])) {
        // Update existing class
        $classId = $_POST['class_id'];
        $gradeLevel = $_POST['gradeLevel'];
        $section = $_POST['section'];

        if (!empty($classId) && !empty($gradeLevel) && !empty($section)) {
            $stmt = $conn->prepare("UPDATE classes SET grade_level = ?, section = ? WHERE class_id = ?");
            $stmt->bind_param("ssi", $gradeLevel, $section, $classId);
            if ($stmt->execute()) {
                $alertMessage = 'Class updated successfully!';
                $alertType = 'success';
            }
            $stmt->close();
        } else {
            $alertMessage = 'Please fill in all fields.';
            $alertType = 'warning';
        }
    } else {
        // Add new class
        $gradeLevel = $_POST['gradeLevel'];
        $section = $_POST['section'];

        if (!empty($gradeLevel) && !empty($section)) {
            $stmt = $conn->prepare("INSERT INTO classes (grade_level, section) VALUES (?, ?)");
            $stmt->bind_param("ss", $gradeLevel, $section);
            if ($stmt->execute()) {
                $alertMessage = 'Class added successfully!';
                $alertType = 'success';
            }
            $stmt->close();
        } else {
            $alertMessage = 'Please fill in all fields.';
            $alertType = 'warning';
        }
    }
}

// Handle deletion of a class
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $classId = $_GET['delete_id'];

    if (!empty($classId)) {
        $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
        $stmt->bind_param("i", $classId);
        if ($stmt->execute()) {
            $alertMessage = 'Class deleted successfully!';
            $alertType = 'success';
        }
        $stmt->close();
    }
}

// Fetch data from database
$classes = [];
$result = $conn->query("SELECT class_id, grade_level, section FROM classes");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

function getStudentsByClassId($class_id) {
    global $conn; // Assuming $conn is your MySQLi connection
    $stmt = $conn->prepare('SELECT studentID, srcode, name FROM student WHERE class_id = ? ORDER BY name ASC');
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getClassDetailsById($class_id) {
    global $conn; // Assuming you are using a global connection

    $sql = "SELECT grade_level, section FROM classes WHERE class_id = ? ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $class_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row;
    } else {
        return null; // or handle as needed
    }
}

?>
