<?php
include 'database/db_connect.php';
session_start();
$today = date("Y-m-d");
echo "Today's date: $today";
$userPosition = trim($_SESSION['position'] ?? '');
echo "<script>console.log('User Position: " . addslashes($userPosition) . "');</script>";

// Define grade conditions based on user position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $gradeCondition = "AND c.grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $gradeCondition = "AND c.grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

// Fetch attendance data for today
$attendanceQuery = "
    SELECT a.attendanceID, a.teacher_Id, a.studentID, a.date, a.status, 
           s.name, s.gender, s.profile_pic, s.teacher_Id AS student_teacher_Id, 
           s.gmail, s.class_id, s.parent_contact, s.school_year,
           c.grade_level, c.section, c.assigned_teacher_id 
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE a.date = '$today' 
    $gradeCondition
    ORDER BY 
        CASE 
            WHEN c.grade_level = 'Kinder' THEN 1
            WHEN c.grade_level = 'Grade-1' THEN 2
            WHEN c.grade_level = 'Grade-2' THEN 3
            WHEN c.grade_level = 'Grade-3' THEN 4
            WHEN c.grade_level = 'Grade-4' THEN 5
            WHEN c.grade_level = 'Grade-5' THEN 6
            WHEN c.grade_level = 'Grade-6' THEN 7
            WHEN c.grade_level = 'Grade-7' THEN 8
            WHEN c.grade_level = 'Grade-8' THEN 9
            WHEN c.grade_level = 'Grade-9' THEN 10
            WHEN c.grade_level = 'Grade-10' THEN 11
            WHEN c.grade_level = 'Grade-11' THEN 12
            WHEN c.grade_level = 'Grade-12' THEN 13
            ELSE 14
        END, 
        s.name ASC";

$attendanceResult = $conn->query($attendanceQuery);

$students = [];
if ($attendanceResult->num_rows > 0) {
    while ($row = $attendanceResult->fetch_assoc()) {
        $students[] = $row;
    }
}

?>
