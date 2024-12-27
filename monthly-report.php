<?php
include 'database/db_connect.php';
session_start();
$userPosition = trim($_SESSION['position'] ?? '');
$class_id = $_SESSION['class_id'] ?? null; // Get class_id from the session

// Check if class_id is available
if (!$class_id) {
    die("Class ID not found. Please ensure you are logged in with a valid teacher account.");
}
// Include Composer's autoloader
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;



// Path to your template Excel file
$templatePath = 'monthly-report.xlsx';

// Load the Excel template
try {
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();
} catch (Exception $e) {
    die('Error loading template file: ' . $e->getMessage());
}

// Query to get months from the attendance table
$sql = "SELECT DISTINCT MONTH(date) AS month_num FROM attendance WHERE studentID IS NOT NULL";
$result = $conn->query($sql);

// Insert the months (from 01 to 12) into row 1 starting from B1
$column = 2; // Start from column B (index 2) for months
$monthCount = [];
if ($result->num_rows > 0) {
    while ($rowData = $result->fetch_assoc()) {
        // Convert the numeric month to the full month name
        $monthNum = $rowData['month_num'];
        $monthName = DateTime::createFromFormat('!m', $monthNum)->format('F'); // Convert numeric month to month name

        // Insert the month name into row 1 (column B, C, D, etc.)
        $sheet->setCellValue(chr(64 + $column) . '1', $monthName); // Using 'B1', 'C1', etc.

        // Query to count the number of distinct days in the current month
        $dayCountSql = "SELECT COUNT(DISTINCT DAY(date)) AS day_count 
                        FROM attendance 
                        WHERE MONTH(date) = $monthNum";
        $dayResult = $conn->query($dayCountSql);

        // Get the count of distinct days for the current month
        if ($dayResult->num_rows > 0) {
            $dayData = $dayResult->fetch_assoc();
            $dayCount = $dayData['day_count']; // Store the count of distinct days
        } else {
            $dayCount = 0; // If no data found, set count to 0
        }

        // Insert the day count into row 2 (column B, C, D, etc.)
        $sheet->setCellValue(chr(64 + $column) . '2', $dayCount); // Row 2 for day count
        $monthCount[$monthNum] = $column; // Store the month number with column index
        $column++; // Move to the next column for the next month
    }
} else {
    echo "No attendance data found.";
}
// Query to get student names where class_id = 1
$sql = "SELECT studentID, name FROM student WHERE class_id = $class_id ORDER BY name ASC";
$result = $conn->query($sql);

// Starting row for data insertion
$row = 4; // Data starts from row 4 (A4)

// Check if the query returns any results
if ($result->num_rows > 0) {
    // Loop through the result set and insert names into the Excel sheet starting at A4
    while ($rowData = $result->fetch_assoc()) {
        // Insert the name directly into column A
        $sheet->setCellValue('A' . $row, $rowData['name']);

        $totalAbsences = 0; // Initialize total absences for each student

        // For each month, count the number of absences for this student
        foreach ($monthCount as $monthNum => $column) {
            // Query to count absences for the current student in the current month
            $absentSql = "SELECT COUNT(*) AS absent_count
                          FROM attendance
                          WHERE studentID = " . $rowData['studentID'] . " 
                          AND MONTH(date) = $monthNum
                          AND status = 'Absent'"; // Assuming 'Absent' is the value in 'status' for absences
            $absentResult = $conn->query($absentSql);

            // Get the count of absences for the student in the current month
            $absentCount = ($absentResult->num_rows > 0) ? $absentResult->fetch_assoc()['absent_count'] : 0;

            // Insert the absence count into the corresponding cell for the student and month
            $sheet->setCellValue(chr(64 + $column) . $row, $absentCount); // Set value in the cell corresponding to student and month

            $totalAbsences += $absentCount; // Add to the total absences for this student
        }

        // Set the total absences per student in column M (row M4 onwards)
        $sheet->setCellValue('M' . $row, $totalAbsences); // Total absences per student

        $row++; // Move to the next row for the next student's name
    }
} else {
    echo "No student data found.";
}

// Query to fetch adviser using class_id directly
$adviserQuery = "
    SELECT firstname, lastname 
    FROM users 
    WHERE class_id = $class_id
    LIMIT 1";
$adviserResult = $conn->query($adviserQuery);

if ($adviserResult && $adviserResult->num_rows > 0) {
    $adviserRow = $adviserResult->fetch_assoc();
    $adviserName = strtoupper($adviserRow['firstname'] . ' ' . $adviserRow['lastname']); // Convert to uppercase
    $sheet->setCellValue('P22', $adviserName);
} else {
    $sheet->setCellValue('P22', 'ADVISER NOT FOUND'); // All caps for error message
}

// Calculate the total number of distinct days in O2 by considering all months
$totalDistinctDays = 0;
$distinctDaysSql = "SELECT COUNT(DISTINCT DATE(date)) AS distinct_day_count 
                    FROM attendance 
                    WHERE studentID IS NOT NULL"; // Count distinct days from the attendance table

$distinctDaysResult = $conn->query($distinctDaysSql);
if ($distinctDaysResult->num_rows > 0) {
    $distinctDaysData = $distinctDaysResult->fetch_assoc();
    $totalDistinctDays = $distinctDaysData['distinct_day_count']; // Get the total number of distinct days
}

// Set the total distinct days into O2
$sheet->setCellValue('O2', $totalDistinctDays);

// Now calculate the total for each row (absences) starting from B4, for N4
for ($i = 4; $i <= $row - 1; $i++) {
    // Calculate O2 - M (total days - total absences for each student)
    $totalAbsencesForStudent = $sheet->getCell('M' . $i)->getValue();  // Get total absences from column M
    $totalDaysInO2 = $sheet->getCell('O2')->getValue(); // Get total days from O2

    // Subtract absences from total days and store in column N
    $sheet->setCellValue('N' . $i, $totalDaysInO2 - $totalAbsencesForStudent); // Set value in column N
}

// Close the database connection
$conn->close();

// Set headers to force download as an Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="monthly_report.xlsx"');
header('Cache-Control: max-age=0');

// Create writer and output to browser
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
