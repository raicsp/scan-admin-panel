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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;



// Path to your template Excel file
$templatePath = 'daily-attendance.xlsx';

// Load the Excel template
try {
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();
} catch (Exception $e) {
    die('Error loading template file: ' . $e->getMessage());
}

// Query for student list
$studentQuery = "SELECT studentID, name FROM student WHERE class_id = $class_id ORDER BY 
        SUBSTRING_INDEX(name, ' ', -1) ASC, 
        SUBSTRING_INDEX(name, ' ', 1) ASC";
$studentResult = $conn->query($studentQuery);

// Setting up month (September) and year
$month = isset($_GET['month']) ? intval($_GET['month']) : 0; // Default to September if not provided
$year = date("Y"); // Current year or specify as needed


// Map each column in row 11 to the correct date
$dateColumnMap = [];
$datesQuery = "SELECT DISTINCT DATE(date) AS attendance_date FROM attendance WHERE MONTH(date) = $month AND YEAR(date) = $year ORDER BY attendance_date ASC";
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
$absentCountPerDate = []; // Array to store absent counts per date (keyed by column)
$lateCountPerDate = [];   // Array to store late counts per date (keyed by column)

if ($studentResult->num_rows > 0) {
    while ($studentRow = $studentResult->fetch_assoc()) {
        $studentID = $studentRow['studentID'];
        $nameParts = explode(' ', $studentRow['name']);
        
        // Assuming the last part is the last name, the first part is the first name, and the rest is middle name/initials
        $firstName = $nameParts[0];
        $lastName = $nameParts[count($nameParts) - 1];
        $middleName = count($nameParts) > 2 ? $nameParts[1] : ''; // Middle name or initial if it exists
        
        // Format as 'lastname, firstname middle' or 'lastname, firstname M.'
        $formattedName = $lastName . ', ' . $firstName;
        if ($middleName) {
            $formattedName .= ' ' . substr($middleName, 0, 1) . '.'; // Add middle initial
        }
    
        $sheet->setCellValue('B' . $row, $formattedName);

        // Fetch this student's attendance for September
        $attendanceQuery = "SELECT DATE(date) AS attendance_date, status FROM attendance 
                            WHERE studentID = $studentID AND MONTH(date) = $month AND YEAR(date) = $year";
        $attendanceResult = $conn->query($attendanceQuery);

        // Variables to count Absent and Late statuses
        $absentCount = 0;
        $lateCount = 0;

        if ($attendanceResult->num_rows > 0) {
            while ($attendanceRow = $attendanceResult->fetch_assoc()) {
                $attendanceDate = $attendanceRow['attendance_date'];
                $status = $attendanceRow['status'];

                if (isset($dateColumnMap[$attendanceDate])) {
                    $column = $dateColumnMap[$attendanceDate]; // Find correct column
                    $cell = $column . $row;

                    if ($status == 'Absent') {
                        // Mark "Absent" as an "X"
                        $sheet->setCellValue($cell, 'X');
                        $absentCount++; // Increment Absent count
                    } elseif ($status == 'Late') {
                        // Apply the "Late" image (instead of triangle)
                        $imagePath = 'late.png'; // Replace with the actual path to the late image

                        if (file_exists($imagePath)) {
                            $drawing = new Drawing();
                            $drawing->setPath($imagePath); // Set image path
                            
                            // Adjust image dimensions (height: 0.76 cm = 28.7 pixels, width: 0.89 cm = 33.7 pixels)
                            $drawing->setHeight(28.7);  // Set height (0.76 cm * 37.795)
                            $drawing->setWidth(33.7);   // Set width (0.89 cm * 37.795)
                            
                            $drawing->setCoordinates($cell); // Place the image in the correct cell
                            $drawing->setWorksheet($sheet); // Add image to sheet
                        } else {
                            echo "Image not found: " . $imagePath;
                        }

                        // Set text alignment for "Late" status (to place it in the lower part of the cell)
                        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
                        $lateCount++; // Increment Late count
                    }
                }
            }
        }

        // Set the Absent and Late counts in columns AC and AD
        $sheet->setCellValue('AC' . $row, $absentCount); // Set Absent count in column AC
        $sheet->setCellValue('AD' . $row, $lateCount);  // Set Late count in column AD

        // Count the absent and late for each date and update
        foreach ($dateColumnMap as $date => $column) {
            $attendanceQuery = "SELECT status FROM attendance WHERE studentID = $studentID 
                                AND DATE(date) = '$date'";
            $attendanceResult = $conn->query($attendanceQuery);

            if ($attendanceResult->num_rows > 0) {
                $status = $attendanceResult->fetch_assoc()['status'];
                if ($status == 'Absent') {
                    if (!isset($absentCountPerDate[$column])) {
                        $absentCountPerDate[$column] = 0;
                    }
                    $absentCountPerDate[$column]++;
                } elseif ($status == 'Late') {
                    if (!isset($lateCountPerDate[$column])) {
                        $lateCountPerDate[$column] = 0;
                    }
                    $lateCountPerDate[$column]++;
                }
            }
        }

        $row++;
    }
}

// Now we calculate the Present count (Total students - Absent - Late) and store it in row 75 starting from D75
$totalStudents = $studentResult->num_rows;  // Get the total number of students
$presentCountTotal = 0; // Initialize total present count for the day

$colIndex = 'D';  // Start from column D
foreach ($dateColumnMap as $date => $column) {
    $absentCount = isset($absentCountPerDate[$column]) ? $absentCountPerDate[$column] : 0;
    $lateCount = isset($lateCountPerDate[$column]) ? $lateCountPerDate[$column] : 0;
    $presentCount = $totalStudents - $absentCount - $lateCount;
    
    // Store Present count in row 75
    $sheet->setCellValue($column . '75', $presentCount);
    $presentCountTotal += $presentCount;  // Update the total present count
}

// Set the total present count in AK75
$sheet->setCellValue('AK75', $presentCountTotal);

// ** Total Number of Students (AJ79-AJ80, AJ83-AJ84) **
$sheet->mergeCells('AJ79:AJ80'); // Merge AJ79 and AJ80
$sheet->setCellValue('AJ79', $totalStudents); // Set total students

$sheet->mergeCells('AJ83:AJ84'); // Merge AJ83 and AJ84
$sheet->setCellValue('AJ83', $totalStudents); // Set total students again

// ** Average Present Count (AJ87) **
// Count the number of days by counting non-empty cells in row 11 (D11 to AB11)
$daysCount = 0;
foreach (range('D', 'AB') as $column) {
    if (!empty($sheet->getCell($column . '11')->getValue())) {
        $daysCount++;
    }
}

// Calculate the average number of Present students per day
if ($daysCount > 0) {
    $averagePresent = $presentCountTotal / $daysCount;
    $sheet->setCellValue('AJ87', $averagePresent); // Set average present count in AJ87
} else {
    $sheet->setCellValue('AJ87', 0); // If no days, set 0
}

$conn->close();

// Set headers to force download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="daily_attendance.xlsx"');
header('Cache-Control: max-age=0');

// Output to browser
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

?>
