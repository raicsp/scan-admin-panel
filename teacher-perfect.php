<?php
include 'database/db_connect.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');
$class_id = $_SESSION['class_id'] ?? '';

// Set default value for the month filter
$month = $_GET['month'] ?? '';

// Build the query to get students with perfect attendance
$perfect_sql = "
SELECT s.studentID, 
       s.name AS student_name, 
       c.grade_level, 
       c.section, 
       s.srcode
FROM student s
JOIN classes c ON s.class_id = c.class_id
JOIN attendance a ON s.studentID = a.studentID
WHERE c.class_id = '$class_id'
";

// Apply month filter if set
if ($month) {
    $perfect_sql .= " AND DATE_FORMAT(a.date, '%Y-%m') = '$month'";
}

// Filter students with only 'Present' records for perfect attendance
$perfect_sql .= "
GROUP BY s.studentID, s.name, c.grade_level, c.section
HAVING COUNT(*) = COUNT(CASE WHEN a.status = 'Present' THEN 1 END)
ORDER BY SUBSTRING_INDEX(s.name, ' ', -1) ASC, SUBSTRING_INDEX(s.name, ' ', 1) ASC
";

$result = $conn->query($perfect_sql);

// Check if there are any results and store them in an array
$perfectAttendanceStudents = [];
if ($result && $result->num_rows > 0) {
    $perfectAttendanceStudents = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Faculty | Laboratory School | Batangas State University TNEU</title>

    <!-- Favicons -->
    <link href="assets/img/bsu.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts and CSS Files -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Students with Perfect Attendance</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="teacher-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Perfect Attendance</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mt-4">Students with Perfect Attendance</h5>
                            <form id="monthFilterForm" class="row g-3">
                                <div class="col-md-5">
                                    <label for="month" class="form-label">Select Month</label>
                                    <input type="month" class="form-control" id="month" name="month" value="<?= htmlspecialchars($month) ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary" id="filterButton">Filter</button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="perfectAttendanceTable">
                                    <thead>
                                        <tr>
                                            <th>Sr-Code</th>
                                            <th>Name</th>
                                            <th>Grade Level</th>
                                            <th>Section</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($perfectAttendanceStudents as $student) : ?>
                                            <tr data-name="<?= htmlspecialchars($student['srcode']) ?>">
                                                <td><?= htmlspecialchars($student['srcode']) ?></td>
                                                <td><?= htmlspecialchars($student['student_name']) ?></td>
                                                <td><?= htmlspecialchars($student['grade_level']) ?></td>
                                                <td><?= htmlspecialchars($student['section']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        // Add click functionality to table rows
        document.addEventListener('DOMContentLoaded', (event) => {
            const table = document.getElementById('perfectAttendanceTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                row.classList.add('clickable-row');
                row.addEventListener('click', function(event) {
                    if (!event.target.closest('.action-buttons')) {
                        const studentName = row.getAttribute('data-name');
                        window.location.href = `student-details.php?srcode=${encodeURIComponent(studentName)}`;
                    }
                });
            }
        });

        // Initialize the DataTable
        const dataTable = new simpleDatatables.DataTable("#perfectAttendanceTable", {
            searchable: true,
            paging: true,
            perPage: 10,
        });

        // Filter button event
        document.getElementById('filterButton').addEventListener('click', function() {
            const month = document.getElementById('month').value;
            const url = new URL(window.location.href);
            if (month) {
                url.searchParams.set('month', month);
            } else {
                url.searchParams.delete('month'); // Clear month filter
            }
            window.location.href = url.toString();
        });
    </script>
</body>

</html>