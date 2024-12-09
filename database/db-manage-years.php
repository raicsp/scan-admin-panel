<?php
include 'database/db_connect.php';
$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}

// Function to fetch archived academic years
function getArchivedYears($conn)
{
    $sql = "SELECT academic_year, date_archived FROM archived_years ORDER BY date_archived DESC";
    $result = $conn->query($sql);

    $archivedData = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $archivedData[] = $row;
        }
    }
    return $archivedData;
}

// Function to archive data from student and attendance to archived tables
function archiveStudentData($conn)
{
    // Fetch distinct academic years from the student table
    $selectAcademicYearSql = "SELECT DISTINCT school_year FROM student";
    $result = $conn->query($selectAcademicYearSql);

    // Insert into archived_years table with the current date for each academic year
    $currentDate = date('Y-m-d'); // Get current date in Y-m-d format
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $academicYear = $row['school_year'];
            $insertArchivedYearSql = "INSERT INTO archived_years (academic_year, date_archived) VALUES ('$academicYear', '$currentDate')";
            if ($conn->query($insertArchivedYearSql) !== TRUE) {
                return "Error inserting into archived_years: " . $conn->error;
            }
        }
    }

    // Archive student data with teacher_name, class_section, and class_grade
    $archiveStudentSql = "
        INSERT INTO archived_student (studentID, name, gender, profile_pic, teacher_Id, teacher_name, gmail, class_id, class_grade, class_section, p_name, parent_contact, school_year, notif)
        SELECT 
            s.studentID, 
            s.name, 
            s.gender, 
            s.profile_pic, 
            s.teacher_Id, 
            CONCAT(u.firstname, ' ', u.lastname) AS teacher_name,
            s.gmail, 
            s.class_id, 
            c.grade_level AS class_grade, 
            c.section AS class_section, 
            s.p_name, 
            s.parent_contact, 
            s.school_year, 
            s.notif
        FROM 
            student s
        LEFT JOIN 
            users u ON s.teacher_Id = u.id
        LEFT JOIN 
            classes c ON s.class_id = c.class_id
    ";

    if ($conn->query($archiveStudentSql) === TRUE) {
        // Delete data from the original student table after archiving
        $deleteStudentSql = "DELETE FROM student";
        if ($conn->query($deleteStudentSql) !== TRUE) {
            return "Error deleting student data: " . $conn->error;
        }
    } else {
        return "Error archiving student data: " . $conn->error;
    }

    // Archive attendance data
    $archiveAttendanceSql = "INSERT INTO archived_attendance (SELECT * FROM attendance)";
    if ($conn->query($archiveAttendanceSql) === TRUE) {
        // Delete data from the original attendance table after archiving
        $deleteAttendanceSql = "DELETE FROM attendance";
        if ($conn->query($deleteAttendanceSql) !== TRUE) {
            return "Error deleting attendance data: " . $conn->error;
        }
    } else {
        return "Error archiving attendance data: " . $conn->error;
    }

    return "success"; // Return success if all operations are completed successfully
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_academic_year'])) {
    $academicYearToDelete = $_POST['academic_year'];

    // Delete the specific archived year
    $deleteSql = "DELETE FROM archived_years WHERE academic_year = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("s", $academicYearToDelete);

    if ($stmt->execute()) {
        $message = "Academic year $academicYearToDelete deleted successfully!";
    } else {
        $message = "Error deleting academic year: " . $conn->error;
    }

    // Refresh the archived data
    $archivedData = getArchivedYears($conn);
}
