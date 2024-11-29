<?php
include 'database/db_connect.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');
$classID = $_SESSION['class_id'] ?? null; // Check if class_id is set in session

$availableGrades = [];
$gradeCondition = '';
// Ensure studentsDaily and studentsMonthly are populated correctly with query results.
$studentsDaily = [];
$studentsMonthly = [];

// Determine available grades based on user position
if ($userPosition === 'Elementary Chairperson') {
    $availableGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6'];
    $gradeCondition = " AND c.grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    $availableGrades = ['Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
    $gradeCondition = " AND c.grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
} else {
    $availableGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6', 
                        'Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
}

// Initialize filter variables
$monthFilter = $_GET['monthFilter'] ?? '';
$gradeFilter = $_GET['gradeFilter'] ?? '';
$sectionFilter = $_GET['sectionFilter'] ?? '';
$gradeFilterMonthly = $_GET['gradeFilterMonthly'] ?? '';
$sectionFilterMonthly = $_GET['sectionFilterMonthly'] ?? '';

// Ensure both grade and section are selected
if (isset($_GET['filterDaily'])) {
    if (!$gradeFilter || !$sectionFilter) {
        echo "Please select both grade and section to generate the daily report.";
        exit;
    }
}

if (isset($_GET['filterMonthly'])) {
    if (!$gradeFilterMonthly || !$sectionFilterMonthly) {
        echo "Please select both grade and section to generate the monthly report.";
        exit;
    }
}

// Query to fetch all sections based on available grades
$gradesAndSectionsQuery = "
    SELECT DISTINCT grade_level, section
    FROM classes
    WHERE grade_level IN ('" . implode("', '", $availableGrades) . "')
    ORDER BY grade_level, section";
$gradesAndSectionsResult = $conn->query($gradesAndSectionsQuery);

$allGrades = [];
$allSectionsByGrade = [];
if ($gradesAndSectionsResult->num_rows > 0) {
    while ($row = $gradesAndSectionsResult->fetch_assoc()) {
        // Add grade level only if it's not already in the allGrades array
        if (!in_array($row['grade_level'], $allGrades)) {
            $allGrades[] = $row['grade_level'];
        }

        // Add section for this grade level
        $allSectionsByGrade[$row['grade_level']][] = $row['section'];
    }
}

// Daily Attendance Query
$dailyQuery = "
    SELECT a.studentID, a.date, a.status, 
           s.name, c.grade_level, c.section, c.class_id
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE 1=1 $gradeCondition";

if ($monthFilter) {
    $dailyQuery .= " AND MONTH(a.date) = '$monthFilter'";
}
if ($gradeFilter) {
    $dailyQuery .= " AND c.grade_level = '$gradeFilter'";
}
if ($sectionFilter) {
    $dailyQuery .= " AND c.section = '$sectionFilter'";
}
if (isset($_SESSION['class_id'])) {
    $dailyQuery .= " AND c.class_id = '{$_SESSION['class_id']}'";
}
$dailyQuery .= " ORDER BY s.name ASC, a.date ASC";
$dailyResult = $conn->query($dailyQuery);

// Monthly Attendance Query
$monthlyQuery = "
    SELECT a.studentID, a.date, a.status, 
           s.name, c.grade_level, c.section
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE 1=1 $gradeCondition";

if ($gradeFilterMonthly) {
    $monthlyQuery .= " AND c.grade_level = '$gradeFilterMonthly'";
}
if ($sectionFilterMonthly) {
    $monthlyQuery .= " AND c.section = '$sectionFilterMonthly'";
}

$monthlyQuery .= " ORDER BY s.name ASC, a.date ASC";
$monthlyResult = $conn->query($monthlyQuery);

// Process results (code to process results goes here)

// Export CSV functionality (can be extended for both daily and monthly reports)
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $filename = 'attendance_report.csv';
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    
    $output = fopen('php://output', 'w');
    $header = array_merge(['Name', 'Grade', 'Section', 'Date', 'Status']);
    fputcsv($output, $header);
    
    $exportData = array_merge($studentsDaily, $studentsMonthly);
    foreach ($exportData as $student) {
        foreach ($student['data'] as $date => $status) {
            fputcsv($output, [$student['name'], $student['grade_level'], $student['section'], $date, $status]);
        }
    }
    fclose($output);
    exit();
}
?>
