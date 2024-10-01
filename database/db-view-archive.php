<?php
// Include your database connection
include 'db_connect.php';
session_start();
$userPosition = trim($_SESSION['position'] ?? '');
echo "<script>console.log('User Position: " . addslashes($userPosition) . "');</script>";

// Define grade conditions based on user position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $gradeCondition = "AND class_grade IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $gradeCondition = "AND class_grade IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

function getArchivedStudents($conn, $schoolYear, $gradeCondition) {
    // Define a custom ordering for grade levels
    $gradeOrder = [
        'Kinder' => 1,
        'Grade-1' => 2,
        'Grade-2' => 3,
        'Grade-3' => 4,
        'Grade-4' => 5,
        'Grade-5' => 6,
        'Grade-6' => 7,
        'Grade-7' => 8,
        'Grade-8' => 9,
        'Grade-9' => 10,
        'Grade-10' => 11,
        'Grade-11' => 12,
        'Grade-12' => 13
    ];

    // Prepare a CASE statement to sort grades based on the custom order
    $orderBy = "CASE class_grade ";
    foreach ($gradeOrder as $grade => $order) {
        $orderBy .= "WHEN '$grade' THEN $order ";
    }
    $orderBy .= "ELSE 14 END"; // Any unknown grades will come last

    // Modify the query to filter by school_year and user position
    $query = "SELECT * FROM archived_student WHERE school_year = ? $gradeCondition ORDER BY $orderBy";
    
    // Prepare and bind the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $schoolYear);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        // Handle query error (optional)
        return [];
    }

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    return $students;
}

function getDistinctGrades($conn, $gradeCondition) {
    // Modify the query to apply the grade condition for distinct grades
    $query = "SELECT DISTINCT class_grade FROM archived_student WHERE 1=1 $gradeCondition ORDER BY class_grade";
    $result = $conn->query($query);

    $grades = [];
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row['class_grade'];
    }

    return $grades;
}

function getDistinctSchoolYears($conn) {
    $query = "SELECT DISTINCT school_year FROM archived_student ORDER BY school_year";
    $result = $conn->query($query);

    $schoolYears = [];
    while ($row = $result->fetch_assoc()) {
        $schoolYears[] = $row['school_year'];
    }

    return $schoolYears;
}

function getSectionsByGrade($conn, $grade) {
    $query = "SELECT DISTINCT class_section FROM archived_student WHERE class_grade = ? ORDER BY class_section";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $grade);
    $stmt->execute();
    $result = $stmt->get_result();

    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row['class_section'];
    }

    return $sections;
}

// Handle AJAX request for sections based on selected grade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade'])) {
    $grade = $_POST['grade'];
    $sections = getSectionsByGrade($conn, $grade);
    echo json_encode($sections);
    exit; // End script execution
}

// Fetch distinct values for the combo boxes, passing the grade condition
$grades = getDistinctGrades($conn, $gradeCondition);
$schoolYears = getDistinctSchoolYears($conn);

?>
