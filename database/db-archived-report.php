<?php
include 'database/db_connect.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');
$classID = $_SESSION['class_id'] ?? null;

if ($userPosition === '') {
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit;
}
$allSectionsByGrade = [];
$sectionQuery = "SELECT DISTINCT class_grade, class_section FROM archived_student";
if ($userPosition === 'Elementary Chairperson') {
    $sectionQuery .= " WHERE class_grade IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    $sectionQuery .= " WHERE class_grade IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

$sectionResult = $conn->query($sectionQuery);
if ($sectionResult && $sectionResult->num_rows > 0) {
    while ($row = $sectionResult->fetch_assoc()) {
        $grade = $row['class_grade'];
        $section = $row['class_section'];
        $allSectionsByGrade[$grade][] = $section;
    }
}


$availableGrades = [];
$allGrades = $availableGrades;
$gradeCondition = '';
$studentsDaily = [];
$studentsMonthly = [];

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
$allGrades = $availableGrades; 

$monthFilter = $_GET['monthFilter'] ?? '';
$gradeFilter = $_GET['gradeFilter'] ?? '';
$sectionFilter = $_GET['sectionFilter'] ?? '';
$gradeFilterMonthly = $_GET['gradeFilterMonthly'] ?? '';
$sectionFilterMonthly = $_GET['sectionFilterMonthly'] ?? '';

if (isset($_GET['filterDaily']) && (!$gradeFilter || !$sectionFilter)) {
    echo "Please select both grade and section to generate the daily report.";
    exit;
}

if (isset($_GET['filterMonthly']) && (!$gradeFilterMonthly || !$sectionFilterMonthly)) {
    echo "Please select both grade and section to generate the monthly report.";
    exit;
}

// Query for Archived Students
$archivedStudentsQuery = "
    SELECT studentID, srcode, name, gender, profile_pic, teacher_Id, teacher_name, gmail, 
           class_id, class_grade, class_section, p_name, parent_contact, school_year, notif
    FROM archived_student
    WHERE 1=1 $gradeCondition";
$archivedStudentsResult = $conn->query($archivedStudentsQuery);

// Query for Archived Attendance
$archivedAttendanceQuery = "
    SELECT attendanceID, teacher_Id, studentID, date, status
    FROM archived_attendance
    WHERE 1=1";
if ($monthFilter) {
    $archivedAttendanceQuery .= " AND MONTH(date) = '$monthFilter'";
}
if ($gradeFilter) {
    $archivedAttendanceQuery .= " AND class_grade = '$gradeFilter'";
}
if ($sectionFilter) {
    $archivedAttendanceQuery .= " AND class_section = '$sectionFilter'";
}
$archivedAttendanceResult = $conn->query($archivedAttendanceQuery);

// Process Archived Data
if ($archivedStudentsResult->num_rows > 0) {
    while ($row = $archivedStudentsResult->fetch_assoc()) {
        $studentsDaily[] = $row; // Process as needed
    }
}

if ($archivedAttendanceResult->num_rows > 0) {
    while ($row = $archivedAttendanceResult->fetch_assoc()) {
        $studentsMonthly[] = $row; // Process as needed
    }
}

// CSV Export
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $filename = 'archived_attendance_report.csv';
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $output = fopen('php://output', 'w');
    $header = ['Name', 'Grade', 'Section', 'Date', 'Status'];
    fputcsv($output, $header);

    foreach ($studentsDaily as $student) {
        fputcsv($output, [$student['name'], $student['class_grade'], $student['class_section'], $student['date'], $student['status']]);
    }
    fclose($output);
    exit();
}
?>
