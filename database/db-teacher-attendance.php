<?php
include 'database/db_connect.php';
session_start();

// Retrieve `class_id` from the session
$classId = $_SESSION['class_id'] ?? '';

// Retrieve start and end dates from request or set default
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date("Y-m-01");
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date("Y-m-d");

// Retrieve optional filters from request
$gradeFilter = isset($_GET['gradeFilter']) ? $_GET['gradeFilter'] : '';
$sectionFilter = isset($_GET['sectionFilter']) ? $_GET['sectionFilter'] : '';
$syFilter = isset($_GET['syFilter']) ? $_GET['syFilter'] : '';

// Query to fetch all sections (no longer filtered by grades)
$gradesAndSectionsQuery = "
    SELECT DISTINCT grade_level, section
    FROM classes
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

// Adjust the attendance query to use `class_id` from the session and optional filters
$attendanceQuery = "
    SELECT a.studentID, a.date, a.status, 
           s.srcode, s.name, c.grade_level, c.section, s.school_year
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE a.date BETWEEN '$startDate' AND '$endDate'
    AND c.class_id = '$classId'";  // Filter by `class_id`

// Apply optional filters for grade level, section, and school year
if ($gradeFilter) {
    $attendanceQuery .= " AND c.grade_level = '$gradeFilter'";
}

if ($sectionFilter) {
    $attendanceQuery .= " AND c.section = '$sectionFilter'";
}

if ($syFilter) {
    $attendanceQuery .= " AND s.school_year = '$syFilter'";
}

// Finalize query ordering
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

        // Initialize status counters if not set
        if (!isset($students[$row['studentID']]['lateCount'])) {
            $students[$row['studentID']]['lateCount'] = 0;
            $students[$row['studentID']]['absentCount'] = 0;
            $students[$row['studentID']]['presentCount'] = 0;
        }
        
        // Count attendance status
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

// Export to CSV if requested
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

        // Append attendance data
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
?>
