<?php
session_start(); // Start the session for flash messages
$alertMessage = '';
$alertType = '';

// Display alert messages if set in session
if (isset($_SESSION['alertMessage'])) {
    $alertMessage = $_SESSION['alertMessage'];
    $alertType = $_SESSION['alertType'];
    unset($_SESSION['alertMessage']);
    unset($_SESSION['alertType']);
}

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
                $_SESSION['alertMessage'] = 'Class updated successfully!';
                $_SESSION['alertType'] = 'success';
            }
            $stmt->close();
        } else {
            $_SESSION['alertMessage'] = 'Please fill in all fields.';
            $_SESSION['alertType'] = 'warning';
        }
    } else {
        // Add new class
        $gradeLevel = $_POST['gradeLevel'];
        $section = $_POST['section'];

        if (!empty($gradeLevel) && !empty($section)) {
            $stmt = $conn->prepare("INSERT INTO classes (grade_level, section) VALUES (?, ?)");
            $stmt->bind_param("ss", $gradeLevel, $section);
            if ($stmt->execute()) {
                $_SESSION['alertMessage'] = 'Class added successfully!';
                $_SESSION['alertType'] = 'success';
            }
            $stmt->close();
        } else {
            $_SESSION['alertMessage'] = 'Please fill in all fields.';
            $_SESSION['alertType'] = 'warning';
        }
    }

    // Redirect to the same page to prevent duplicate submissions
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle deletion of a class
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $classId = $_GET['delete_id'];

    if (!empty($classId)) {
        $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
        $stmt->bind_param("i", $classId);
        if ($stmt->execute()) {
            $_SESSION['alertMessage'] = 'Class deleted successfully!';
            $_SESSION['alertType'] = 'success';
        }
        $stmt->close();
    }

    // Redirect to avoid re-deleting on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch the logged-in user's position
$userPosition = trim($_SESSION['position'] ?? '');

// Define grade condition based on the user's position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $gradeCondition = "WHERE grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $gradeCondition = "WHERE grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

// Fetch classes based on the grade condition
$classes = [];
$query = "SELECT class_id, grade_level, section FROM classes $gradeCondition";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
} else {
    echo 'No classes found for your assigned grade levels.';
}

// Function to get students by class_id
function getStudentsByClassId($class_id) {
    global $conn;
    $stmt = $conn->prepare('SELECT studentID, srcode, name FROM student WHERE class_id = ? ORDER BY name ASC');
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get class details by class_id
function getClassDetailsById($class_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT grade_level, section FROM classes WHERE class_id = ?");
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row;
    } else {
        return null; // or handle as needed
    }
}


?>