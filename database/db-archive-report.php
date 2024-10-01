<?php
include 'database/db_connect.php';
session_start();

// Capture and sanitize the academic year from the URL (GET parameter)
$academicYear = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';
$academicYear = preg_replace('/[^0-9\-]/', '', $academicYear); // Sanitize input

// Capture user position from the session
$userPosition = trim($_SESSION['position'] ?? '');
echo "<script>console.log('User Position: " . addslashes($userPosition) . "');</script>";

// Define grade conditions based on user position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $gradeCondition = " AND s.class_grade IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $gradeCondition = " AND s.class_grade IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

// Retrieve start and end dates, filters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date("Y-m-01");
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date("Y-m-d");
$sectionFilter = isset($_GET['sectionFilter']) ? $_GET['sectionFilter'] : '';

// Use the captured academic year in the query
$attendanceQuery = "
    SELECT 
        s.studentID, 
        s.name, 
        a.date, 
        a.status, 
        s.school_year
    FROM 
        archived_student s
    JOIN 
        archived_attendance a ON a.studentID = s.studentID
    WHERE 
        a.date BETWEEN '$startDate' AND '$endDate' 
        AND s.school_year = '$academicYear' 
        $gradeCondition";  // Filter by the academic year and grade condition

// Adding the section filter
if ($sectionFilter) {
    $attendanceQuery .= " AND s.class_section = '$sectionFilter'";
}

$attendanceQuery .= " ORDER BY s.name ASC, a.date ASC";

// Execute the query
$attendanceResult = $conn->query($attendanceQuery);

$students = [];
$dates = [];

// Process query results for attendance
if ($attendanceResult->num_rows > 0) {
    while ($row = $attendanceResult->fetch_assoc()) {
        $students[$row['studentID']]['name'] = $row['name'];
        $students[$row['studentID']]['data'][$row['date']] = $row['status'];
        $students[$row['studentID']]['school_year'] = $row['school_year'];

        if (!in_array($row['date'], $dates)) {
            $dates[] = $row['date'];
        }
    }
}

// Export to CSV if requested
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, array_merge(['Name'], $dates)); // CSV header

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

// HTML for the grade level combo box can go here
?>
