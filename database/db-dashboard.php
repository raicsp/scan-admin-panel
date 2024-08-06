<?php
include 'database/db_connect.php';
// Get today's date
$today = date("Y-m-d");
$currentYear = date("Y");
$first_day_of_month = date("Y-m-01");

// Query to get the count of students present today
$sql_present = "SELECT COUNT(*) as count FROM attendance WHERE date = '$today' AND status = 'Present'";
$result_present = $conn->query($sql_present);
$row_present = $result_present->fetch_assoc();
$present_today = $row_present['count'];

// Query to get the count of students late today
$sql_late = "SELECT COUNT(*) as count FROM attendance WHERE date = '$today' AND status = 'Late'";
$result_late = $conn->query($sql_late);
$row_late = $result_late->fetch_assoc();
$late_today = $row_late['count'];

// Query to get the count of students absent today
$sql_absent = "SELECT COUNT(*) as count FROM attendance WHERE date = '$today' AND status = 'Absent'";
$result_absent = $conn->query($sql_absent);
$row_absent = $result_absent->fetch_assoc();
$absent_today = $row_absent['count'];

// Query to get the count of teachers
$sql_teacher = "SELECT COUNT(*) AS total_teachers FROM `users`"; // Adjust condition as needed
$result_teacher = $conn->query($sql_teacher); // Make sure to use the correct query variable
$row_teacher = $result_teacher->fetch_assoc();
$teacher = $row_teacher['total_teachers']; // Use the alias 'total_teachers'

// Query to get the count of students
$sql_student = "SELECT COUNT(*) AS total_student FROM `student`"; // Adjust condition as needed
$result_student = $conn->query($sql_student); // Make sure to use the correct query variable
$row_student = $result_student->fetch_assoc();
$student = $row_student['total_student']; // Use the alias 'total_teachers'

// Query to get attendance data by grade
$query = "
SELECT c.grade_level, COUNT(a.studentID) AS attendance_count 
FROM attendance a
JOIN student s ON a.studentID = s.studentID
JOIN classes c ON s.class_id = c.class_id
WHERE a.date = '$today'
GROUP BY c.grade_level
ORDER BY c.grade_level ASC;
";
$result = $conn->query($query);

$grades = [];
$attendanceCounts = [];

while ($row = $result->fetch_assoc()) {
    $grades[] = '' . $row['grade_level'];
    $attendanceCounts[] = $row['attendance_count'];
}

$attendance_by_grade = [
    'labels' => $grades,
    'datasets' => [
        [
            'label' => 'Attendance',
            'data' => $attendanceCounts,
            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'borderWidth' => 1
        ]
    ]
];


// Query to get monthly attendance data
$sql_monthly_attendance = "
  SELECT 
    MONTHNAME(date) as month, 
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent
  FROM attendance 
  WHERE YEAR(date) = '$currentYear'
  GROUP BY MONTH(date)
  ORDER BY MONTH(date) ASC";

$result_monthly_attendance = $conn->query($sql_monthly_attendance);

$months = [];
$presentData = [];
$absentData = [];

while ($row = $result_monthly_attendance->fetch_assoc()) {
    $months[] = $row['month'];
    $presentData[] = $row['present'];
    $absentData[] = $row['absent'];
}

$stacked_bar_data = [
    'labels' => $months,
    'datasets' => [
        [
            'label' => 'Present',
            'data' => $presentData,
            'backgroundColor' => '#FF1654'
        ],
        [
            'label' => 'Absent',
            'data' => $absentData,
            'backgroundColor' => '#247BA0'
        ]
    ]
];
// Query to get attendance data for the line chart
$query = "
SELECT DATE_FORMAT(date, '%d') as day, 
SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_count,
SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_count
FROM attendance
WHERE date BETWEEN '$first_day_of_month' AND '$today'
GROUP BY date
ORDER BY date ASC";

$result = $conn->query($query);

$days = [];
$presentCounts = [];
$absentCounts = [];

while ($row = $result->fetch_assoc()) {
    $days[] = $row['day'];
    $presentCounts[] = $row['present_count'];
    $absentCounts[] = $row['absent_count'];
}

$attendance_overview = [
    'days' => $days,
    'presentCounts' => $presentCounts,
    'absentCounts' => $absentCounts
];

// Fetch top students with most absences
$absences_sql = "SELECT s.studentID, CONCAT(s.name) AS student_name, 
                 c.grade_level, COUNT(a.status) AS absence_count, 
                 ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
                 FROM attendance a
                 JOIN student s ON a.studentID = s.studentID
                 JOIN classes c ON s.class_id = c.class_id
                 WHERE a.status = 'Absent' 
                 GROUP BY s.studentID
                 ORDER BY absence_count DESC
                 LIMIT 5";

$absences_result = $conn->query($absences_sql);

// Fetch top students with most late
$late_sql = "SELECT s.studentID, CONCAT(s.name) AS student_name, 
             c.grade_level, COUNT(a.status) AS late_count, 
             ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
             FROM attendance a
             JOIN student s ON a.studentID = s.studentID
             JOIN classes c ON s.class_id = c.class_id
             WHERE a.status = 'Late'
             GROUP BY s.studentID
             ORDER BY late_count DESC
             LIMIT 5";

$late_result = $conn->query($late_sql);

// Close connection
$conn->close();
?>
