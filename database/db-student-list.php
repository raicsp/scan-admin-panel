<?php
include 'database/db_connect.php';

// Initialize response array
$response = ['success' => false, 'error' => ''];

// Fetch student data
$query = "
    SELECT 
        s.studentID,
        s.name AS student_name,
        s.gender,
        c.grade_level,
        c.section,
        s.parent_contact,
        s.gmail AS parent_email,
        s.school_year
    FROM 
        student s
    JOIN 
        classes c ON s.teacher_id = c.assigned_teacher_id
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
        s.name ASC
";
$result = $conn->query($query);
$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Fetch unique grade levels
$gradeQuery = "SELECT DISTINCT grade_level FROM classes";
$gradeResult = $conn->query($gradeQuery);
$grades = [];

while ($row = $gradeResult->fetch_assoc()) {
    $grades[] = $row['grade_level'];
}

// Fetch unique sections
$sectionQuery = "SELECT DISTINCT section FROM classes";
$sectionResult = $conn->query($sectionQuery);
$sections = [];

while ($row = $sectionResult->fetch_assoc()) {
    $sections[] = $row['section'];
}
