<?php
include 'database/db_connect.php';
session_start();
$userPosition = trim($_SESSION['position'] ?? '');

// Fetch grade and section from the GET parameters
$gradeFilter = $_GET['gradeFilterMonthly'] ?? '';
$sectionFilter = $_GET['sectionFilterMonthly'] ?? '';

// Validate grade and section selection
if (!$gradeFilter || !$sectionFilter) {
    die("Please select both grade and section to generate the monthly report.");
}

// Query to find the correct class_id based on the selected grade and section
$classQuery = "
    SELECT class_id 
    FROM classes 
    WHERE grade_level = '$gradeFilter' 
    AND section = '$sectionFilter'
";
$classResult = $conn->query($classQuery);

if ($classResult->num_rows === 0) {
    die("Class not found for the selected grade and section.");
}

$classRow = $classResult->fetch_assoc();
$class_id = $classRow['class_id'];

// Proceed with the existing code but using the fetched class_id
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Path to the template file
$templatePath = 'monthly-report.xlsx';

// Load the Excel template
try {
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();
} catch (Exception $e) {
    die('Error loading template file: ' . $e->getMessage());
}

// Insert months into row 1 (from B1) and calculate distinct days per month
$sql = "SELECT DISTINCT MONTH(date) AS month_num FROM attendance WHERE studentID IS NOT NULL";
$result = $conn->query($sql);
$column = 2; 
$monthCount = [];
if ($result->num_rows > 0) {
    while ($rowData = $result->fetch_assoc()) {
        $monthNum = $rowData['month_num'];
        $monthName = DateTime::createFromFormat('!m', $monthNum)->format('F');
        
        $sheet->setCellValue(chr(64 + $column) . '1', $monthName);
        
        $dayCountSql = "SELECT COUNT(DISTINCT DAY(date)) AS day_count FROM attendance WHERE MONTH(date) = $monthNum";
        $dayResult = $conn->query($dayCountSql);
        $dayData = $dayResult->fetch_assoc();
        $dayCount = $dayData['day_count'] ?? 0;

        $sheet->setCellValue(chr(64 + $column) . '2', $dayCount);
        $monthCount[$monthNum] = $column;
        $column++;
    }
}

// Fetch students for the selected grade and section
$sql = "SELECT studentID, name FROM student WHERE class_id = $class_id ORDER BY name ASC";
$result = $conn->query($sql);
$row = 4; 

if ($result->num_rows > 0) {
    while ($rowData = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $rowData['name']);
        $totalAbsences = 0;

        foreach ($monthCount as $monthNum => $column) {
            $absentSql = "SELECT COUNT(*) AS absent_count
                          FROM attendance
                          WHERE studentID = " . $rowData['studentID'] . " 
                          AND MONTH(date) = $monthNum
                          AND status = 'Absent'";
            $absentResult = $conn->query($absentSql);
            $absentCount = $absentResult->fetch_assoc()['absent_count'] ?? 0;

            $sheet->setCellValue(chr(64 + $column) . $row, $absentCount);
            $totalAbsences += $absentCount;
        }

        $sheet->setCellValue('M' . $row, $totalAbsences);
        $row++;
    }
} else {
    echo "No student data found.";
}
// Fetch adviser
$adviserQuery = "
    SELECT u.firstname, u.lastname 
    FROM users u
    JOIN classes c ON u.class_id = c.class_id
    WHERE c.grade_level = '$gradeFilter' AND c.section = '$sectionFilter'
    LIMIT 1";
$adviserResult = $conn->query($adviserQuery);

if ($adviserResult && $adviserResult->num_rows > 0) {
    $adviserRow = $adviserResult->fetch_assoc();
    $adviserName = strtoupper($adviserRow['firstname'] . ' ' . $adviserRow['lastname']); // Convert to uppercase
    $sheet->setCellValue('P22', $adviserName); // Insert adviser's name in cell AF103
} else {
    $sheet->setCellValue('P22', 'ADVISER NOT FOUND'); // All caps for error message
}

$totalDistinctDays = 0;
$distinctDaysSql = "SELECT COUNT(DISTINCT DATE(date)) AS distinct_day_count FROM attendance WHERE studentID IS NOT NULL";
$distinctDaysResult = $conn->query($distinctDaysSql);
$totalDistinctDays = $distinctDaysResult->fetch_assoc()['distinct_day_count'] ?? 0;

$sheet->setCellValue('O2', $totalDistinctDays);

for ($i = 4; $i <= $row - 1; $i++) {
    $totalAbsencesForStudent = $sheet->getCell('M' . $i)->getValue();
    $totalDaysInO2 = $sheet->getCell('O2')->getValue();
    $sheet->setCellValue('N' . $i, $totalDaysInO2 - $totalAbsencesForStudent);
}

$conn->close();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="monthly_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
?>
