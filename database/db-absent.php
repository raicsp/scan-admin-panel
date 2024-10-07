<?php
include 'database/db_connect.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');

// Determine allowed grade levels based on user position
$allowedGrades = [];
if ($userPosition === 'Elementary Chairperson') {
    $allowedGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6'];
} elseif ($userPosition === 'High School Chairperson') {
    $allowedGrades = ['Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
}

// Construct grade condition for the SQL query
$gradeCondition = '';
if (!empty($allowedGrades)) {
    $gradeList = "'" . implode("', '", $allowedGrades) . "'";
    $gradeCondition = "AND c.grade_level IN ($gradeList)";
}

// Query to get students with most absences
$absences_sql = "
    SELECT s.studentID, CONCAT(s.name) AS student_name, 
           c.grade_level, c.section, COUNT(a.status) AS absence_count, 
           ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE a.status = 'Absent' 
    $gradeCondition  -- Include the grade condition here
    GROUP BY s.studentID
    ORDER BY absence_count DESC
";

$result = $conn->query($absences_sql);

// Check if there are any results and store them in an array
$absentStudents = [];
if ($result && $result->num_rows > 0) {
    $absentStudents = $result->fetch_all(MYSQLI_ASSOC);
}

// Query to get grade levels and sections
$sectionQuery = "SELECT grade_level, section FROM classes WHERE grade_level IN ($gradeList)";
$sectionResult = $conn->query($sectionQuery);

$gradeSections = [];
while ($row = $sectionResult->fetch_assoc()) {
    $gradeSections[$row['grade_level']][] = $row['section'];
}

// Pass the sections data to JavaScript
echo "<script>var gradeSections = " . json_encode($gradeSections) . ";</script>";

$conn->close();
?>
