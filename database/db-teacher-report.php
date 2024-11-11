<?php
include 'database/db_connect.php';
session_start();
$userPosition = trim($_SESSION['position'] ?? '');
$class_id = $_SESSION['class_id'] ?? null; // Get class_id from the session

// Check if class_id is available
if (!$class_id) {
    die("Class ID not found. Please ensure you are logged in with a valid teacher account.");
}

// Retrieve start and end dates, filters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date("Y-m-01");
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date("Y-m-d");
$gradeFilter = isset($_GET['gradeFilter']) ? $_GET['gradeFilter'] : '';
$sectionFilter = isset($_GET['sectionFilter']) ? $_GET['sectionFilter'] : '';
$syFilter = isset($_GET['syFilter']) ? $_GET['syFilter'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';

// Query to fetch all sections based on the teacher's class_id
$gradesAndSectionsQuery = "
    SELECT DISTINCT grade_level, section
    FROM classes
    WHERE class_id = '$class_id'
    ORDER BY grade_level, section";

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

// Adjust the query to include class_id, and any optional filters for grade level, section, and school year
$attendanceQuery = "
    SELECT a.studentID, a.date, a.status, 
           s.name, s.srcode, c.grade_level, c.section, s.school_year
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE c.class_id = '$class_id'";

// Apply the date range if both start and end dates are provided
if ($startDate && $endDate) {
    $attendanceQuery .= " AND a.date BETWEEN '$startDate' AND '$endDate'";
}

// Apply the month-only filter if a month is selected
if ($month) {
    $attendanceQuery .= " AND MONTH(a.date) = '$month'";
}

// Additional filters (if any)
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
        
        $students[$row['studentID']]['srcode'] = $row['srcode'];
        $students[$row['studentID']]['name'] = $row['name'];
        $students[$row['studentID']]['data'][$row['date']] = $row['status'];
        $students[$row['studentID']]['grade_level'] = $row['grade_level'];
        $students[$row['studentID']]['section'] = $row['section'];
        $students[$row['studentID']]['school_year'] = $row['school_year'];

        // Count statuses
        if (!isset($students[$row['studentID']]['lateCount'])) {
            $students[$row['studentID']]['lateCount'] = 0;
            $students[$row['studentID']]['absentCount'] = 0;
            $students[$row['studentID']]['presentCount'] = 0;
        }

        switch ($row['status']) {
            case 'Late':
                $students[$row['studentID']]['lateCount']++;
                break;
            case 'Absent':
                $students[$row['studentID']]['absentCount']++;
                break;
            case 'Present':
                $students[$row['studentID']]['presentCount']++;
                break;
        }

        // Populate sections by grade
        $sectionsByGrade[$row['grade_level']][] = $row['section'];

        if (!in_array($row['date'], $dates)) {
            $dates[] = $row['date'];
        }
    }
}


// HTML for the grade level combo box
