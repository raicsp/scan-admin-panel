<?php
session_start();
include 'db_connect.php';

// Initialize arrays for grades and sections
$alertMessage = '';
$alertType = '';

// Fetch the logged-in user's position
$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}

// Define grade condition based on the user's position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $gradeCondition = "WHERE grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $gradeCondition = "WHERE grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

// Fetch grades based on the grade condition
$grades = [];
$query = "SELECT DISTINCT grade_level FROM classes $gradeCondition ORDER BY CAST(SUBSTRING_INDEX(grade_level, '-', -1) AS UNSIGNED) ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row['grade_level'];
    }
} else {
    echo 'No grades found for your assigned grade levels.';
}

// Fetch sections for each grade based on the grade condition
$sections = [];
$query = "SELECT grade_level, section FROM classes $gradeCondition ORDER BY grade_level, section";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sections[$row['grade_level']][] = $row['section'];
    }
} else {
    echo 'No sections found for your assigned grade levels.';
}


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if CSV import button was clicked
    if (isset($_POST['import_csv'])) {
        $gradeLevel = $_POST['grade_level'];
        $section = $_POST['section'];

        // Fetch class_id and teacher_id based on selected grade level and section
        $query = "SELECT class_id, assigned_teacher_id FROM classes WHERE grade_level = ? AND section = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $gradeLevel, $section);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $class_id = $row['class_id'];
            $teacher_id = $row['assigned_teacher_id']; // Get the teacher ID if exists

            // Determine the school year based on the current date
            $currentYear = date("Y");
            $currentMonth = date("n"); // Numeric representation of the current month (1 to 12)

            if ($currentMonth >= 8) {
                // If the current month is August or later
                $schoolYear = "{$currentYear}-" . ($currentYear + 1);
            } else {
                // If the current month is before August
                $schoolYear = ($currentYear - 1) . "-{$currentYear}";
            }

            // Handle CSV file upload
            if ($_FILES['csv_file']['name']) {
                $filename = $_FILES['csv_file']['tmp_name'];
                if (($handle = fopen($filename, "r")) !== FALSE) {
                    fgetcsv($handle); // Skip header row

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        // Get data from CSV
                        $srcode = $data[0];
                        $name = $data[1];
                        $gender = $data[2];
                        $gmail = $data[3];
                        $p_name = $data[4];
                        $parent_contact = $data[5];

                        // Set defaults for columns not in CSV
                        $profile_pic = '';  // Default or empty
                        $notif = NULL;        // Default or empty

                        // Insert student data into the database
                        $query = "INSERT INTO student (srcode, name, gender, profile_pic, teacher_id, gmail, class_id, p_name, parent_contact, school_year, notif) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ssssisissss', $srcode, $name, $gender, $profile_pic, $teacher_id, $gmail, $class_id, $p_name, $parent_contact, $schoolYear, $notif);

                        if (!$stmt->execute()) {
                            $_SESSION['alertMessage'] = 'Error adding student: ' . $stmt->error;
                            $_SESSION['alertType'] = 'danger';
                            break; // Stop processing on first error
                        }
                    }

                    fclose($handle);
                    $_SESSION['alertMessage'] = "CSV Import successful.";
                    $_SESSION['alertType'] = "success";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $_SESSION['alertMessage'] = "Error opening the CSV file.";
                    $_SESSION['alertType'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
        } else {
            $_SESSION['alertMessage'] = 'No class found for the selected grade level and section.';
            $_SESSION['alertType'] = 'danger';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else if (isset($_POST['submit_individual'])) {
        // Handle individual form submission
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
                $srcode = isset($_POST['srcode']) ? $_POST['srcode'] : null;
                $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
                $studentName = isset($_POST['student_name']) ? $_POST['student_name'] : null;
                $p_name =  isset($_POST['parent_name']) ? $_POST['parent_name'] : null;
                $parentContact = isset($_POST['parent_contact']) ? $_POST['parent_contact'] : null;
                $parentEmail = isset($_POST['parent_email']) ? $_POST['parent_email'] : null; // Parent email is now optional
                $notif = NULL;

                // Determine the school year
                $currentYear = date("Y");
                $currentMonth = date("n"); // Numeric representation of the current month (1 to 12)
                if ($currentMonth >= 8) {
                    // If the current month is August or later
                    $schoolYear = "{$currentYear}-" . ($currentYear + 1);
                } else {
                    // If the current month is before August
                    $schoolYear = ($currentYear - 1) . "-{$currentYear}";
                }

                // Validate student information
                if ($studentName && $parentContact) {
                    // Insert student record into class_list
                    $insertQuery = "INSERT INTO student (srcode, gender, name, p_name, parent_contact, gmail, school_year, class_id, teacher_id, notif) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param('ssssssssss', $srcode, $gender, $studentName, $p_name, $parentContact, $parentEmail, $schoolYear, $classId, $teacherId, $notif);

                    if (!$insertStmt->execute()) {
                        $_SESSION['alertMessage'] = 'Error adding student: ' . $insertStmt->error;
                        $_SESSION['alertType'] = 'danger';
                    } else {
                        $_SESSION['alertMessage'] = 'Student added successfully!';
                        $_SESSION['alertType'] = 'success';
                    }
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $_SESSION['alertMessage'] = 'Please fill in all required student information.';
                    $_SESSION['alertType'] = 'warning';
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            } else {
                $_SESSION['alertMessage'] = 'Class not found for the selected grade and section.';
                $_SESSION['alertType'] = 'error';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            $_SESSION['alertMessage'] = 'Please select a grade level and section.';
            $_SESSION['alertType'] = 'warning';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Redirect to prevent form resubmission on reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
