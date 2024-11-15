<?php
include 'database/db_connect.php';
session_start();

// Check user position
$userPosition = trim($_SESSION['position'] ?? '');

// Include Composer's autoloader
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Get filters from query parameters
$grade = $_GET['gradeFilter'] ?? '';
$section = $_GET['sectionFilter'] ?? '';
$month = isset($_GET['monthFilter']) ? intval($_GET['monthFilter']) : 0; // Default to September if not provided
$year = date("Y"); // Current year

// Validate if both grade and section are selected
if (empty($grade) || empty($section)) {
    die("Please select both grade and section to generate the report.");
}

// Path to your template Excel file
$templatePath = 'daily-attendance.xlsx';

// Load the Excel template
try {
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();
} catch (Exception $e) {
    die('Error loading template file: ' . $e->getMessage());
}

// Query to fetch students by grade and section, ordered by name
$studentQuery = "SELECT studentID, name 
                 FROM student s
                 JOIN classes c ON s.class_id = c.class_id
                 WHERE c.grade_level = '$grade' AND c.section = '$section' 
                 ORDER BY name ASC";
$studentResult = $conn->query($studentQuery);

// Map each column in row 11 to the correct date for the selected month
$dateColumnMap = [];
$datesQuery = "SELECT DISTINCT DATE(date) AS attendance_date 
               FROM attendance a
               JOIN student s ON a.studentID = s.studentID
               JOIN classes c ON s.class_id = c.class_id
               WHERE c.grade_level = '$grade' AND c.section = '$section' 
               AND MONTH(date) = $month AND YEAR(date) = $year
               ORDER BY attendance_date ASC";
$datesResult = $conn->query($datesQuery);

$currentColumn = 'D';
if ($datesResult->num_rows > 0) {
    while ($dateRow = $datesResult->fetch_assoc()) {
        $attendanceDate = new DateTime($dateRow['attendance_date']);
        $weekdayNum = $attendanceDate->format('N'); // 1 (Mon) - 5 (Fri)
        if ($weekdayNum < 6) { // Only map Monday-Friday
            $sheet->setCellValue($currentColumn . '11', $attendanceDate->format('j')); // Date
            $dateColumnMap[$attendanceDate->format('Y-m-d')] = $currentColumn; // Map date to column
            $currentColumn++;
        }
    }
}

// Insert students in Column B starting at B14 and their attendance in Row 14 onward
$row = 14;
$absentCountPerDate = [];
$lateCountPerDate = [];

if ($studentResult->num_rows > 0) {
    while ($studentRow = $studentResult->fetch_assoc()) {
        $studentID = $studentRow['studentID'];
        $sheet->setCellValue('B' . $row, $studentRow['name']);

        // Fetch attendance for each student
        $attendanceQuery = "SELECT DATE(date) AS attendance_date, status 
                            FROM attendance 
                            WHERE studentID = $studentID 
                            AND MONTH(date) = $month 
                            AND YEAR(date) = $year";
        $attendanceResult = $conn->query($attendanceQuery);

        $absentCount = 0;
        $lateCount = 0;

        if ($attendanceResult->num_rows > 0) {
            while ($attendanceRow = $attendanceResult->fetch_assoc()) {
                $attendanceDate = $attendanceRow['attendance_date'];
                $status = $attendanceRow['status'];

                if (isset($dateColumnMap[$attendanceDate])) {
                    $column = $dateColumnMap[$attendanceDate];
                    $cell = $column . $row;

                    if ($status == 'Absent') {
                        $sheet->setCellValue($cell, 'X');
                        $absentCount++;
                    } elseif ($status == 'Late') {
                        $imagePath = 'late.png';
                        if (file_exists($imagePath)) {
                            $drawing = new Drawing();
                            $drawing->setPath($imagePath);
                            $drawing->setHeight(28.7);
                            $drawing->setWidth(33.7);
                            $drawing->setCoordinates($cell);
                            $drawing->setWorksheet($sheet);
                        }
                        $lateCount++;
                    }
                }
            }
        }

        // Set counts for Absent and Late in columns AC and AD
        $sheet->setCellValue('AC' . $row, $absentCount);
        $sheet->setCellValue('AD' . $row, $lateCount);
        $row++;
    }
}

// Calculate Present count for each date and set it in row 75
$totalStudents = $studentResult->num_rows;
$presentCountTotal = 0;

foreach ($dateColumnMap as $date => $column) {
    $absentCount = $absentCountPerDate[$column] ?? 0;
    $lateCount = $lateCountPerDate[$column] ?? 0;
    $presentCount = $totalStudents - $absentCount - $lateCount;
    $sheet->setCellValue($column . '75', $presentCount);
    $presentCountTotal += $presentCount;
}

// Set the total present count in AK75
$sheet->setCellValue('AK75', $presentCountTotal);

// Set the total number of students in AJ79 and AJ83
$sheet->mergeCells('AJ79:AJ80');
$sheet->setCellValue('AJ79', $totalStudents);
$sheet->mergeCells('AJ83:AJ84');
$sheet->setCellValue('AJ83', $totalStudents);

// Calculate the average number of Present students per day in AJ87
$daysCount = count($dateColumnMap);
$averagePresent = $daysCount > 0 ? $presentCountTotal / $daysCount : 0;
$sheet->setCellValue('AJ87', $averagePresent);

$conn->close();

// Set headers to force download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="daily_attendance.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
?>
