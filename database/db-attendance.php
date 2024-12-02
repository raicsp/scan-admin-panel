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
    $availableGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6', 'Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
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
        ORDER BY 
        CASE 
            WHEN grade_level = 'Kinder' THEN 1
            WHEN grade_level = 'Grade-1' THEN 2
            WHEN grade_level = 'Grade-2' THEN 3
            WHEN grade_level = 'Grade-3' THEN 4
            WHEN grade_level = 'Grade-4' THEN 5
            WHEN grade_level = 'Grade-5' THEN 6
            WHEN grade_level = 'Grade-6' THEN 7
            WHEN grade_level = 'Grade-7' THEN 8
            WHEN grade_level = 'Grade-8' THEN 9
            WHEN grade_level = 'Grade-9' THEN 10
            WHEN grade_level = 'Grade-10' THEN 11
            WHEN grade_level = 'Grade-11' THEN 12
            WHEN grade_level = 'Grade-12' THEN 13
        END,
        section";

// Execute the query
$gradesAndSectionsResult = $conn->query($gradesAndSectionsQuery);

// Prepare arrays to hold all grades and sections
$allGrades = [];
$allSectionsByGrade = [];

// Process the results to populate grades and sections
if ($gradesAndSectionsResult->num_rows > 0) {
    while ($row = $gradesAndSectionsResult->fetch_assoc()) {
        $grade = $row['grade_level'];
        $section = $row['section'];

        // Add the grade level only once
        if (!in_array($grade, $allGrades)) {
            $allGrades[] = $grade;
        }

        // Group sections under the respective grade
        $allSectionsByGrade[$grade][] = $section;
    }
}
// Adjust the query to include grade level, section, and school year filters
$attendanceQuery = "
    SELECT a.studentID, a.date, a.status, 
           s.srcode,s.name, c.grade_level, c.section, s.school_year
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

// Initialize totals
$totalPresent = 0;
$totalAbsent = 0;
$totalLate = 0;

// (Export to CSV if requested)
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $filename = '';

    if ($gradeFilter) {
        $filename .= "{$gradeFilter}";
    }

    if ($sectionFilter) {
        $filename .= "_{$sectionFilter}";
    }

    $filename .= "_attendance_report.csv";

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");



    $output = fopen('php://output', 'w');
    $header = array_merge(['Name', 'Total Number of Present', 'Total Number of Absent', 'Total Number of Late'], $dates);
    fputcsv($output, $header);

    foreach ($students as $student) {
        // Create the row with totals first
        $row = [
            $student['name'],
            $student['presentCount'],
            $student['absentCount'],
            $student['lateCount']
        ];

        // Append the attendance data
        foreach ($dates as $date) {
            $row[] = isset($student['data'][$date]) ? $student['data'][$date] : 'Absent';
        }

        // Accumulate totals
        $totalPresent += $student['presentCount'];
        $totalAbsent += $student['absentCount'];
        $totalLate += $student['lateCount'];

        fputcsv($output, $row);
    }



    fclose($output);
    exit();
}

// HTML for the grade level combo box
