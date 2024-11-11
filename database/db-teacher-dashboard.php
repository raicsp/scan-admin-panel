<?php
include 'database/db_connect.php';
session_start();

$class_id = $_SESSION['class_id'] ?? 0;

if ($class_id === 0) {
    die("Class ID is required to display data.");
}


$today = date("Y-m-d");
$first_day_of_week = date("Y-m-d", strtotime('monday this week'));
$first_day_of_month = date("Y-m-01");

$filter = $_GET['filter'] ?? 'today';

switch ($filter) {
    case 'week':
        $date_condition = "date BETWEEN '$first_day_of_week' AND '$today'";
        break;
    case 'month':
        $date_condition = "date BETWEEN '$first_day_of_month' AND '$today'";
        break;
    case 'today':
    default:
        $date_condition = "date = '$today'";
        break;
}

function getCount($conn, $class_id, $date_condition, $status) {
    $sql = "SELECT COUNT(*) as count 
            FROM attendance a
            JOIN student s ON a.studentID = s.studentID
            JOIN classes c ON s.class_id = c.class_id
            WHERE $date_condition AND a.status = '$status' AND c.class_id = '$class_id'";
    $result = $conn->query($sql);
    return $result ? $result->fetch_assoc()['count'] : 0;
}
$present_today = getCount($conn, $class_id, $date_condition, 'Present');
$late_today = getCount($conn, $class_id, $date_condition, 'Late');
$absent_today = getCount($conn, $class_id, $date_condition, 'Absent');

$sql_student = "SELECT COUNT(DISTINCT s.studentID) AS total_student
                FROM student s
                JOIN classes c ON s.class_id = c.class_id
                WHERE c.class_id = '$class_id'";
$result_student = $conn->query($sql_student);
$student = $result_student ? $result_student->fetch_assoc()['total_student'] : 0;

// Modify the student name format and order by last name
$absences_sql = "SELECT s.studentID, 
                 s.name AS student_name, 
                 c.grade_level, c.section, COUNT(a.status) AS absence_count, 
                    s.srcode,
                 ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
                 FROM attendance a
                 JOIN student s ON a.studentID = s.studentID
                 JOIN classes c ON s.class_id = c.class_id
                 WHERE a.status = 'Absent' AND c.class_id = '$class_id'
                 GROUP BY s.studentID
                 ORDER BY SUBSTRING_INDEX(s.name, ' ', -1) ASC, SUBSTRING_INDEX(s.name, ' ', 1) ASC
                 LIMIT 5";
$absences_result = $conn->query($absences_sql);

$late_sql = "SELECT s.studentID, 
            s.name AS student_name, 
             c.grade_level, c.section, COUNT(a.status) AS late_count, 
             s.srcode,
             ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
             FROM attendance a
             JOIN student s ON a.studentID = s.studentID
             JOIN classes c ON s.class_id = c.class_id
             WHERE a.status = 'Late' AND c.class_id = '$class_id'
             GROUP BY s.studentID
             ORDER BY SUBSTRING_INDEX(s.name, ' ', -1) ASC, SUBSTRING_INDEX(s.name, ' ', 1) ASC
             LIMIT 5";
$late_result = $conn->query($late_sql);

$perfect_attendance_sql = "SELECT s.studentID, 
                           s.name AS student_name, 
                           c.grade_level, c.section, s.srcode
                           FROM student s
                           JOIN classes c ON s.class_id = c.class_id
                           JOIN attendance a ON s.studentID = a.studentID
                           WHERE c.class_id = '$class_id'
                           GROUP BY s.studentID, s.name, c.grade_level, c.section
                           HAVING COUNT(*) = COUNT(CASE WHEN a.status = 'Present' THEN 1 END)
                           ORDER BY SUBSTRING_INDEX(s.name, ' ', -1) ASC, SUBSTRING_INDEX(s.name, ' ', 1) ASC
                           LIMIT 5";
$perfect_attendance_result = $conn->query($perfect_attendance_sql);

// Get gender distribution
$gender_query = "SELECT gender, COUNT(*) as count FROM student WHERE class_id = '$class_id' GROUP BY gender";
$gender_result = $conn->query($gender_query);

$male_count = 0;
$female_count = 0;
while ($row = $gender_result->fetch_assoc()) {
    if ($row['gender'] == 'Male') {
        $male_count = $row['count'];
    } elseif ($row['gender'] == 'Female') {
        $female_count = $row['count'];
    }
}

// Count each Attendance Status
$status_query = "
    SELECT a.status, COUNT(*) as count
    FROM attendance a
    INNER JOIN student s ON a.studentID = s.studentID
    WHERE s.class_id = '$class_id' AND a.date = CURDATE()
    GROUP BY a.status
";

$status_result = $conn->query($status_query);

// Initialize counters
$present_count = 0;
$absent_count = 0;
$late_count = 0;

while ($row = $status_result->fetch_assoc()) {
    if ($row['status'] == 'Present') {
        $present_count = $row['count'];
    } elseif ($row['status'] == 'Absent') {
        $absent_count = $row['count'];
    } elseif ($row['status'] == 'Late') {
        $late_count = $row['count'];
    }
}


// Monthly Attendance Query
$monthlyDataQuery = "
    SELECT DATE_FORMAT(date, '%b') AS month, COUNT(*) AS presentCount
    FROM attendance
    JOIN student ON attendance.studentID = student.studentID
    WHERE status = 'Present' AND YEAR(date) = YEAR(CURDATE()) AND student.class_id = $class_id
    GROUP BY MONTH(date)
    ORDER BY MONTH(date);
";
$monthlyDataResult = $conn->query($monthlyDataQuery);

$months = [];
$presentCounts = [];

while ($row = $monthlyDataResult->fetch_assoc()) {
    $months[] = $row['month'];
    $presentCounts[] = $row['presentCount'];
}

// Daily Attendance Query 
$dailyDataQuery = " 
   SELECT DATE_FORMAT(date, '%a') AS day, COUNT(*) AS presentCount
   FROM attendance
   JOIN student ON attendance.studentID = student.studentID
   WHERE status = 'Present' AND WEEK(date) = WEEK(CURDATE()) AND student.class_id = $class_id
   GROUP BY day;
";

$dailyDataResult = $conn->query($dailyDataQuery);  // Correct variable name here

$dailyDays = [];  // Changed to avoid conflict
$dailyPresentCounts = [];  // Changed to avoid conflict

while ($row = $dailyDataResult->fetch_assoc()) {  // Correct variable name here
    $dailyDays[] = $row['day'];  // Use 'day' here instead of 'month'
    $dailyPresentCounts[] = $row['presentCount'];
}

$conn->close();
?>
