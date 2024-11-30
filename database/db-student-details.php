<?php
include 'database/db_connect.php'; // Include the database connection
// Get student srcode from query parameter
$studentSrCode = isset($_GET['srcode']) ? $_GET['srcode'] : null;

if ($studentSrCode === null) {
  echo "No student selected.";
  exit();
}

// Fetch student details based on the student srcode
$query = "SELECT studentID, srcode, name, gender, profile_pic, gmail, p_name, parent_contact, grade_level, section 
          FROM student 
          JOIN classes ON student.class_id = classes.class_id 
          WHERE srcode = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentSrCode);  // Use the srcode parameter
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $studentID = $row['studentID'];
  $studentName = $row['name'];
  $srcode = $row['srcode'];
  $gender = $row['gender'];
  $profilePic = $row['profile_pic']; // BLOB data
  $gmail = $row['gmail'];
  $p_name = $row['p_name'];
  $parentContact = $row['parent_contact'];
  $gradeLevel = $row['grade_level'];
  $section = $row['section'];

  // Convert BLOB to base64 string if a profile pic exists
  $profilePicBase64 = base64_encode($profilePic);
} else {
  echo "Student not found";
  exit();
}

$defaultProfilePic = 'assets/img/default.png';
$profilePicSrc = $profilePicBase64 ? "data:image/jpeg;base64,$profilePicBase64" : $defaultProfilePic;

// Fetching monthly attendance summary data for the student (same as before)
$monthlyAttendanceData = [];
$attendanceByCategoryData = [];

// Monthly Attendance trends
$query = "SELECT MONTH(date) as month, 
                 SUM(status='Present') as days_present, 
                 SUM(status='Absent') as days_absent,
                 SUM(status='Late') as days_late
          FROM attendance 
          WHERE studentID = ? 
          GROUP BY MONTH(date)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $monthlyAttendanceData[] = $row;
}

// Attendance distribution
$currentMonth = date('m'); // Gets the current month as a two-digit number
$currentYear = date('Y');  // Gets the current year as a four-digit number

$query = "SELECT status, COUNT(*) as count 
          FROM attendance 
          WHERE studentID = ? 
          AND YEAR(date) = ? 
          AND MONTH(date) = ? 
          AND status IN ('Present', 'Late', 'Absent') 
          GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $studentID, $currentYear, $currentMonth);
$stmt->execute();
$result = $stmt->get_result();

$attendanceByCategoryData = [];
while ($row = $result->fetch_assoc()) {
    $attendanceByCategoryData[] = $row;
}



// Fetching all attendance records for the student
$query = "SELECT date, status FROM attendance WHERE studentID = ? ORDER BY date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$attendanceResult = $stmt->get_result();

$attendanceRecords = [];
while ($row = $attendanceResult->fetch_assoc()) {
  $attendanceRecords[] = $row;
}

$stmt->close();
$conn->close();
?>