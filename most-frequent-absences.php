<?php
include 'database/db_connect.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');

// Determine allowed grade levels based on user position
$allowedGrades = [];
if ($userPosition === 'Elementary Chairperson') {
    $allowedGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6'];
} elseif ($userPosition === 'High School Chairperson') {
    $allowedGrades = ['Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
} else {
    $allowedGrades = ['Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6', 'Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12'];
}

// Retrieve date range or month from request, if provided
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$month = $_GET['month'] ?? '';

// Construct grade condition for the SQL query
$gradeCondition = '';
if (!empty($allowedGrades)) {
    $gradeList = "'" . implode("', '", $allowedGrades) . "'";
    $gradeCondition = "AND c.grade_level IN ($gradeList)";
}

// Set default values for date range if month or specific date range is not provided
if ($month) {
    $startOfMonth = $month . "-01";
    $endOfMonth = date("Y-m-t", strtotime($startOfMonth)); // Last day of the month
    $dateCondition = "AND a.date BETWEEN '$startOfMonth' AND '$endOfMonth'";
} elseif ($startDate && $endDate) {
    $dateCondition = "AND a.date BETWEEN '$startDate' AND '$endDate'";
    $startOfMonth = $startDate; // For use in percentage calculation
    $endOfMonth = $endDate;
} else {
    // Fallback to the current month if no date or month provided
    $startOfMonth = date("Y-m-01");
    $endOfMonth = date("Y-m-t");
    $dateCondition = "AND a.date BETWEEN '$startOfMonth' AND '$endOfMonth'";
}

// Retrieve selected grade-section filter
$selectedGradeSection = $_GET['grade_section'] ?? '';

// Grade and section condition
$gradeSectionCondition = '';
if ($selectedGradeSection) {
    // Check if the selected grade-section is in the format "Grade X - Section"
    if (strpos($selectedGradeSection, ' - ') !== false) {
        [$grade, $section] = explode(' - ', $selectedGradeSection);
        $gradeSectionCondition = "AND c.grade_level = '" . $conn->real_escape_string($grade) . "' AND c.section = '" . $conn->real_escape_string($section) . "'";
    } else {
        // Only grade level is selected (e.g., "Grade X" without a section)
        $gradeSectionCondition = "AND c.grade_level = '" . $conn->real_escape_string($selectedGradeSection) . "' AND c.section = 'N/A'";
    }
}

// Query to get students with most absences within the date range
$absences_sql = "
SELECT 
    s.srcode, 
    s.studentID, 
    CONCAT(s.name) AS student_name, 
    c.grade_level, 
    c.section, 
    COUNT(a.status) AS absence_count, 
    ROUND(
        (COUNT(a.status) / NULLIF(
            (SELECT COUNT(*) 
             FROM attendance 
             WHERE studentID = s.studentID 
             AND a.date BETWEEN '$startOfMonth' AND '$endOfMonth'), 0)
        ) * 100, 
        2
    ) AS percentage
FROM attendance a
JOIN student s ON a.studentID = s.studentID
JOIN classes c ON s.class_id = c.class_id
WHERE a.status = 'Absent' 
$gradeCondition
$dateCondition
$gradeSectionCondition
GROUP BY s.srcode, s.studentID, s.name, c.grade_level, c.section
ORDER BY absence_count DESC;
";

$result = $conn->query($absences_sql);

// Check if there are any results and store them in an array
$absentStudents = [];
if ($result && $result->num_rows > 0) {
    $absentStudents = $result->fetch_all(MYSQLI_ASSOC);
}

// Query to get grade levels and sections
$sectionQuery = "SELECT DISTINCT grade_level, section FROM classes WHERE grade_level IN ($gradeList)";
$sectionResult = $conn->query($sectionQuery);

$gradeSections = [];
while ($row = $sectionResult->fetch_assoc()) {
    if ($row['section'] === 'N/A') {
        // If the section is 'N/A', only show the grade level (no section)
        $gradeSections[] = $row['grade_level'];
    } else {
        // If there's a section, show both grade and section
        $gradeSections[] = $row['grade_level'] . ' - ' . $row['section'];
    }
}

// Pass the sections data to JavaScript
echo "<script>var gradeSections = " . json_encode($gradeSections) . ";</script>";
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Administrator | Laboratory School | Batangas State University TNEU</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/bsu.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            <h1>Most Frequent Absences</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Most Frequent Absences</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Students with Most Frequent Absences<br>

                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="gradeSectionFilter">Select Grade & Section:</label>
                                    <select id="gradeSectionFilter" class="form-select">
                                        <option value="">Select Grade & Section</option>
                                        <?php foreach ($gradeSections as $gradeSection): ?>
                                            <option value="<?= htmlspecialchars($gradeSection) ?>"><?= htmlspecialchars($gradeSection) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="monthPicker">Select Month:</label>
                                    <input type="month" id="monthPicker" class="form-control" name="month">
                                </div>
                                <div class="col-md-3" style="margin-top: 23px;">

                                    <button id="filterButton" class="btn btn-primary w-100">Filter</button>
                                </div>
                                <div class="col-md-3" style="margin-top: 23px;">
                                    <button id="clearButton" class="btn btn-secondary w-100 ms-2">Clear</button>
                                </div>
                            </div>


                            <!-- Table for displaying the students with the most absences -->
                             <div class= "table-responsive">
                            <table class="table table-bordered table-hover" id="absenceTable">
                                <thead>
                                    <tr>
                                        <th>Sr-Code</th>
                                        <th>Name</th>
                                        <th>Grade Level</th>
                                        <th>Section</th>
                                        <th>Absences</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($absentStudents as $student) : ?>
                                        <tr data-name="<?= htmlspecialchars($student['srcode']) ?>">
                                            <td><?= htmlspecialchars($student['srcode']) ?></td>
                                            <td><?= htmlspecialchars($student['student_name']) ?></td>
                                            <td><?= htmlspecialchars($student['grade_level']) ?></td>
                                            <td><?= htmlspecialchars($student['section']) ?></td>
                                            <td><?= htmlspecialchars($student['absence_count']) ?></td>
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
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataTable = new simpleDatatables.DataTable("#absenceTable", {
                searchable: true,
                paging: true,
                perPage: 10,
                fixedHeight: true,
                labels: {
                    placeholder: "Search...",
                    perPage: "entries per page",
                    noRows: "No results found",
                    info: "Showing {start} to {end} of {rows} results"
                }
            });
            document.getElementById('filterButton').addEventListener('click', function() {
                const gradeSection = document.getElementById('gradeSectionFilter').value;
                const month = document.getElementById('monthPicker').value;

                if (gradeSection || month) {
                    const url = new URL(window.location.href);
                    if (gradeSection) url.searchParams.set('grade_section', gradeSection);
                    if (month) url.searchParams.set('month', month);

                    window.location.href = url.toString();
                } else {
                    // Show SweetAlert if no filters are selected
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Filters Selected',
                        text: 'Please select at least one filter to proceed.',
                        confirmButtonText: 'OK',
                    });
                }
            });

            document.getElementById('clearButton').addEventListener('click', function() {
                const url = new URL(window.location.href);
                url.search = ""; // Clear all query parameters
                window.location.href = url.toString();
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Add click functionality to table rows in Code 2
            const table = document.getElementById('absenceTable');
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
    </script>
