<?php
include 'database/db_connect.php';
session_start();
$userPosition = trim($_SESSION['position'] ?? '');


$gradeCondition = '';
$availableGrades = [];

// Determine available grades based on user position
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $availableGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6'];
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $availableGrades = ['Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
} else {
    $availableGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6','Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
}
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $gradeCondition = " AND c.grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $gradeCondition = " AND c.grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

// Retrieve start and end dates, filters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date("Y-m-01");
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date("Y-m-d");
$gradeFilter = isset($_GET['gradeFilter']) ? $_GET['gradeFilter'] : '';
$sectionFilter = isset($_GET['sectionFilter']) ? $_GET['sectionFilter'] : '';
$syFilter = isset($_GET['syFilter']) ? $_GET['syFilter'] : '';

// Query to fetch all sections based on available grades
$gradesAndSectionsQuery = "
    SELECT DISTINCT grade_level, section
    FROM classes
    WHERE grade_level IN ('" . implode("', '", $availableGrades) . "')
    ORDER BY grade_level, section";

// Execute the query
$gradesAndSectionsResult = $conn->query($gradesAndSectionsQuery);

// Prepare arrays to hold all grades and sections
$allGrades = [];
$allSectionsByGrade = [];

// Process the results to populate grades and sections
if ($gradesAndSectionsResult->num_rows > 0) {
    while ($row = $gradesAndSectionsResult->fetch_assoc()) {
        $allGrades[] = $row['grade_level'];
        $allSectionsByGrade[$row['grade_level']][] = $row['section'];
    }
}

// Adjust the query to include grade level, section, and school year filters
$attendanceQuery = "
    SELECT a.studentID, a.date, a.status, 
           s.name, c.grade_level, c.section, s.school_year
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE a.date BETWEEN '$startDate' AND '$endDate' $gradeCondition";

if ($gradeFilter) {
    $attendanceQuery .= " AND c.grade_level = '$gradeFilter'";
}

if ($sectionFilter) {
    $attendanceQuery .= " AND c.section = '$sectionFilter'";
}
if ($syFilter) {
    $attendanceQuery .= " AND s.school_year = '$syFilter'";
}

$attendanceQuery .= " ORDER BY s.name ASC, a.date ASC";

$attendanceResult = $conn->query($attendanceQuery);

$students = [];
$dates = [];
$sectionsByGrade = [];

// Process query results for attendance
if ($attendanceResult->num_rows > 0) {
    while ($row = $attendanceResult->fetch_assoc()) {
        $students[$row['studentID']]['name'] = $row['name'];
        $students[$row['studentID']]['data'][$row['date']] = $row['status'];
        $students[$row['studentID']]['grade_level'] = $row['grade_level'];
        $students[$row['studentID']]['section'] = $row['section'];
        $students[$row['studentID']]['school_year'] = $row['school_year'];

        // Populate sections by grade
        $sectionsByGrade[$row['grade_level']][] = $row['section'];

        if (!in_array($row['date'], $dates)) {
            $dates[] = $row['date'];
        }
    }
}

// Get unique sections for each grade level
$uniqueSectionsByGrade = [];
foreach ($sectionsByGrade as $grade => $sections) {
    $uniqueSectionsByGrade[$grade] = array_unique($sections);
}

// Export to CSV if requested
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_report.csv"');

    $output = fopen('php://output', 'w');
    $header = array_merge(['Name'], $dates);
    fputcsv($output, $header);

    foreach ($students as $student) {
        $row = [$student['name']];
        foreach ($dates as $date) {
            $row[] = isset($student['data'][$date]) ? $student['data'][$date] : 'Absent';
        }
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

// HTML for the grade level combo box
?>