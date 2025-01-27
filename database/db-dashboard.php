<?php
include 'database/db_connect.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}


// Define grade conditions based on user position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
    // Allow access only to Kinder to Grade-6
    $gradeCondition = "AND c.grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
    // Allow access only to Grade-7 to Grade-12
    $gradeCondition = "AND c.grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}
// Get today's date
$today = date("Y-m-d");
$first_day_of_week = date("Y-m-d", strtotime('monday this week'));
$first_day_of_month = date("Y-m-01");

// Set default filter to 'today'
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$grade = isset($_GET['grade']) ? $_GET['grade'] : '';

// Fetch unique grades
$gradesQuery = "SELECT DISTINCT grade_level FROM classes ORDER BY grade_level";
$gradesResult = $conn->query($gradesQuery);
$grades = [];

while ($row = $gradesResult->fetch_assoc()) {
    $grades[] = $row['grade_level'];
}

// Adjust SQL query based on selected filter
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

// Adjust SQL query based on selected grade
$grade_condition = $grade ? "AND c.grade_level = '$grade'" : "";

// Query to get the count of students present
$sql_present = "
    SELECT COUNT(*) as count 
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE $date_condition AND status = 'Present' $grade_condition $gradeCondition";
$result_present = $conn->query($sql_present);
$present_today = $result_present->fetch_assoc()['count'];

$sql_present_card = "
    SELECT COUNT(*) as count 
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE date= '$today' AND status = 'Present' $gradeCondition";
$result_present_card = $conn->query($sql_present_card);
$present_today_card = $result_present_card->fetch_assoc()['count'];


// Query to get the count of students late
$sql_late = "
    SELECT COUNT(*) as count 
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE $date_condition AND status = 'Late' $grade_condition $gradeCondition";
$result_late = $conn->query($sql_late);
$late_today = $result_late->fetch_assoc()['count'];

$sql_late_card = "
    SELECT COUNT(*) as count 
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE date= '$today' AND status = 'Late' $gradeCondition";
$result_late_card = $conn->query($sql_late_card);
$late_today_card = $result_late_card->fetch_assoc()['count'];

// Query to get the count of students absent
$sql_absent = "
    SELECT COUNT(*) as count 
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id 
    WHERE $date_condition AND status = 'Absent' $gradeCondition";
$result_absent = $conn->query($sql_absent);
$absent_today = $result_absent->fetch_assoc()['count'];

$sql_absent_card = "
    SELECT COUNT(*) as count 
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id 
    WHERE date = '$today' AND status = 'Absent' $gradeCondition";
$result_absent_card = $conn->query($sql_absent_card);
$absent_today_card = $result_absent_card->fetch_assoc()['count'];

// Check if no data is available
$total_count = $present_today + $late_today + $absent_today;

if ($total_count == 0) {
    $present_today = $late_today = $absent_today = 0;
}

// Query to get the count of teachers
$sql_teacher = "SELECT COUNT(DISTINCT u.id) AS total_teachers
    FROM users u
    JOIN classes c ON u.class_id = c.class_id
    WHERE 1=1 $gradeCondition"; // Adjust condition as needed
$result_teacher = $conn->query($sql_teacher); // Make sure to use the correct query variable
$row_teacher = $result_teacher->fetch_assoc();
$teacher = $row_teacher['total_teachers']; // Use the alias 'total_teachers'

// Query to get the count of students
$sql_student = "SELECT COUNT(DISTINCT s.studentID) AS total_student
    FROM student s
    JOIN classes c ON s.class_id = c.class_id
    WHERE 1=1 $gradeCondition"; // Adjust condition as needed
$result_student = $conn->query($sql_student); // Make sure to use the correct query variable
$row_student = $result_student->fetch_assoc();
$student = $row_student['total_student']; // Use the alias 'total_teachers'


// Query to get gender distribution based on grade filter pie chart
$grade = isset($_GET['gendergrade']) ? $_GET['gendergrade'] : '';

// Build the WHERE clause
$where_clause = "1"; // Default to no filtering
if (!empty($grade)) {
    // Filter only by grade level, disregarding sections
    $where_clause .= " AND c.grade_level = '$grade'";
}

// Query to fetch gender distribution for the selected grade
$query = "
  SELECT s.gender, COUNT(*) as count
  FROM student s
  INNER JOIN classes c ON s.class_id = c.class_id
  WHERE $where_clause $gradeCondition
  GROUP BY s.gender
";
$result = $conn->query($query);

// Initialize gender counts
$male_count = $female_count = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['gender'] == 'Male') {
            $male_count = $row['count'];
        } elseif ($row['gender'] == 'Female') {
            $female_count = $row['count'];
        }
    }
}

// Query to get monthly attendance data
// stacked chart Attendance overview by Grade
$today = date("Y-m-d");
$currentYear = date("Y");
$first_day_of_month = date("Y-m-01");

// Get selected school year from URL parameters, default to current year if not set
$selectedSchoolYear = isset($_GET['school_year']) ? $_GET['school_year'] : $currentYear;

// Query to get monthly attendance data
$today = date("Y-m-d");
$currentYear = date("Y");
$first_day_of_month = date("Y-m-01");

// Get selected school year from URL parameters
$selectedSchoolYear = isset($_GET['school_year']) ? $_GET['school_year'] : null;

// If no school year is selected, try to set a default school year based on attendance data
if (!$selectedSchoolYear) {
    $sql_check_attendance = "
        SELECT DISTINCT s.school_year
        FROM attendance a
        JOIN student s ON a.studentID = s.studentID
        WHERE YEAR(date) = '$currentYear'
        ORDER BY s.school_year DESC
        LIMIT 1";

    $result_check_attendance = $conn->query($sql_check_attendance);

    if ($result_check_attendance->num_rows > 0) {
        $row = $result_check_attendance->fetch_assoc();
        $selectedSchoolYear = $row['school_year'];
    } else {
        // Fallback to the most recent school year if no data is found for the current year
        $sql_fallback_year = "SELECT DISTINCT school_year FROM student ORDER BY school_year DESC LIMIT 1";
        $result_fallback_year = $conn->query($sql_fallback_year);

        if ($result_fallback_year->num_rows > 0) {
            $row = $result_fallback_year->fetch_assoc();
            $selectedSchoolYear = $row['school_year'];
        }
    }
}


// Query to get attendance data for the line chart
$query = "
SELECT DATE_FORMAT(date, '%Y-%m') as month, 
       DATE_FORMAT(date, '%M %Y') as month_name
FROM attendance
GROUP BY month, month_name
ORDER BY month ASC";

$result = $conn->query($query);

$months = [];
while ($row = $result->fetch_assoc()) {
    $months[] = $row;
}

$month_options = [];
foreach ($months as $month) {
    $month_options[] = $month;
}

// Get the selected month from the URL
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date("Y-m");
$dateTime = DateTime::createFromFormat('Y-m', $selectedMonth);

// Format the selected month as "Y-F" (e.g., "2024-July")
$formattedMonth = $dateTime->format('F-Y');

// Fetch data for the selected month
$first_day_of_month = $selectedMonth . "-01";
$last_day_of_month = date("Y-m-t", strtotime($selectedMonth));

$query = "
SELECT DATE_FORMAT(a.date, '%d') as day, 
       SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count,
       SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent_count,
       SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late_count
FROM attendance a
JOIN student s ON a.studentID = s.studentID
JOIN classes c ON s.class_id = c.class_id
WHERE a.date BETWEEN '$first_day_of_month' AND '$last_day_of_month'
$gradeCondition  -- Include the grade condition here
GROUP BY a.date, day  -- Include `a.date` in the GROUP BY clause
ORDER BY a.date ASC;";

$result = $conn->query($query);

$days = [];
$presentCounts = [];
$absentCounts = [];
$lateCounts = [];

while ($row = $result->fetch_assoc()) {
    $days[] = $row['day'];
    $presentCounts[] = $row['present_count'];
    $absentCounts[] = $row['absent_count'];
    $lateCounts[] = $row['late_count'];
}

$attendance_overview = [
    'days' => $days,
    'presentCounts' => $presentCounts,
    'absentCounts' => $absentCounts,
    'lateCounts' => $lateCounts
];


// Fetch top students with most absences
$absences_sql = "  
    SELECT s.srcode, s.studentID, CONCAT(s.name) AS student_name, 
           c.grade_level, c.section, COUNT(a.status) AS absence_count, 
           ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE a.status = 'Absent' 
    AND MONTH(a.date) = MONTH(CURDATE()) 
    $gradeCondition  -- Include the grade condition here
    GROUP BY s.studentID, s.srcode, s.name, c.grade_level, c.section  -- Add the necessary fields in GROUP BY
    ORDER BY absence_count DESC
    LIMIT 5";


$absences_result = $conn->query($absences_sql);

// Fetch top students with most late
$late_sql = "  
    SELECT s.srcode, s.studentID, CONCAT(s.name) AS student_name, 
             c.grade_level, c.section, COUNT(a.status) AS late_count, 
             ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE a.status = 'Late'
    AND MONTH(a.date) = MONTH(CURDATE()) 
    $gradeCondition
    GROUP BY s.studentID, s.srcode, s.name, c.grade_level, c.section  -- Add the necessary fields in GROUP BY
    ORDER BY late_count DESC
    LIMIT 5";


$late_result = $conn->query($late_sql);

// Fetch students with perfect attendance
$perfect_attendance_sql = "
  SELECT 
    s.srcode,
    s.studentID, 
    CONCAT(s.name) AS student_name, 
    c.grade_level, 
    c.section
FROM 
    student s
JOIN 
    classes c ON s.class_id = c.class_id
JOIN 
    attendance a ON s.studentID = a.studentID
WHERE 
    1=1
    $gradeCondition
     AND MONTH(a.date) = MONTH(CURDATE()) 
GROUP BY 
    s.studentID, s.srcode, s.name, c.grade_level, c.section  -- Add necessary fields in GROUP BY
HAVING 
    COUNT(*) = COUNT(CASE WHEN a.status = 'Present' THEN 1 END)
LIMIT 5;
";


$perfect_attendance_result = $conn->query($perfect_attendance_sql);

//bar chart
// Initialize selected school year
$schoolYear = isset($_POST['schoolYear']) ? $_POST['schoolYear'] : '';

// Check if an AJAX request is made to fetch attendance data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the date and school year from the AJAX request
    $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); // Default to today

    // Query to get attendance data by grade
    $query = "
    SELECT c.grade_level, 
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_count,
           SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) AS late_count,
           SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent_count
    FROM attendance a
    JOIN student s ON a.studentID = s.studentID
    JOIN classes c ON s.class_id = c.class_id
    WHERE a.date = '$date'
    " . (!empty($schoolYear) ? "AND s.school_year = '$schoolYear' " : "") . "
    $gradeCondition  -- Include grade condition here
    GROUP BY c.grade_level
    ORDER BY 
        CASE
            WHEN c.grade_level = 'Kinder' THEN 0
            WHEN c.grade_level = 'Grade-1' THEN 1
            WHEN c.grade_level = 'Grade-2' THEN 2
            WHEN c.grade_level = 'Grade-3' THEN 3
            WHEN c.grade_level = 'Grade-4' THEN 4
            WHEN c.grade_level = 'Grade-5' THEN 5
            WHEN c.grade_level = 'Grade-6' THEN 6
            WHEN c.grade_level = 'Grade-7' THEN 7
            WHEN c.grade_level = 'Grade-8' THEN 8
            WHEN c.grade_level = 'Grade-9' THEN 9
            WHEN c.grade_level = 'Grade-10' THEN 10
            WHEN c.grade_level = 'Grade-11' THEN 11
            WHEN c.grade_level = 'Grade-12' THEN 12
            ELSE 13
        END ASC;
";


    $result = $conn->query($query);

    $grades = [];
    $presentCounts = [];
    $lateCounts = [];
    $absentCounts = [];

    while ($row = $result->fetch_assoc()) {
        $grades[] = $row['grade_level'];
        $presentCounts[] = $row['present_count'];
        $lateCounts[] = $row['late_count'];
        $absentCounts[] = $row['absent_count'];
    }

    $attendance_by_grade = [
        'labels' => $grades,
        'datasets' => [
            [
                'label' => 'Present',
                'data' => $presentCounts,
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1
            ],
            [
                'label' => 'Late',
                'data' => $lateCounts,
                'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                'borderColor' => 'rgba(255, 159, 64, 1)',
                'borderWidth' => 1
            ],
            [
                'label' => 'Absent',
                'data' => $absentCounts,
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1
            ]
        ]
    ];

    // Send the attendance data back to the AJAX request
    echo json_encode($attendance_by_grade);
    exit; // Stop further execution after sending data
}

// Fetch school years for dropdown
$school_years = [];
$school_year_query = "SELECT DISTINCT(school_year) FROM student"; // Adjust this query based on your database structure
$school_year_result = $conn->query($school_year_query);

while ($row = $school_year_result->fetch_assoc()) {
    $school_years[] = $row['school_year']; // Change to the correct column name
}

//function for grade level dropdown
function renderGradeDropdown($conn, $userPosition, $selectedGrade = '')
{
    // Determine grade condition based on user position
    $gradeCondition = '';
    if ($userPosition === 'Elementary Chairperson') {
        $gradeCondition = "WHERE grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
    } elseif ($userPosition === 'High School Chairperson') {
        $gradeCondition = "WHERE grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
    }

    // Fetch grades based on the condition
    $query = "SELECT DISTINCT grade_level FROM classes $gradeCondition";
    $result = $conn->query($query);

    // Start building the dropdown
    $output = '';
    $output .= '<option value="">All Grades</option>'; // Default option

    while ($row = $result->fetch_assoc()) {
        $grade = htmlspecialchars($row['grade_level']);
        $selected = ($grade === $selectedGrade) ? 'selected' : '';
        $output .= "<option value='$grade' $selected>$grade</option>";
    }

    $output .= '</select>';
    return $output;
}

// Close connection
$conn->close();
