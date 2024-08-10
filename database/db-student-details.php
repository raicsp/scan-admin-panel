<?php
include 'database/db_connect.php'; // Include the database connection
$studentName = $_GET['name'];
// Fetching student ID based on student name
$query = "SELECT studentID FROM student WHERE name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentName);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $studentID = $row['studentID'];
} else {
  // Handle case where student is not found
  echo "Student not found";
  exit();
}

// Fetching monthly attendance summary data for the student
$monthlyAttendanceData = [];
$attendanceByCategoryData = [];

// Monthly Attendance Summary
$query = "SELECT MONTH(date) as month, SUM(status='present') as days_present, SUM(status='absent') as days_absent FROM attendance WHERE studentID = ? GROUP BY MONTH(date)";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $monthlyAttendanceData[] = $row;
}

// Attendance by Category
$query = "SELECT status, COUNT(*) as count FROM attendance WHERE studentID = ? GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $attendanceByCategoryData[] = $row;
}


$stmt->close();
$conn->close();
?>