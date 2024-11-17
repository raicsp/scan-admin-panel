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

// Retrieve date range from request, if provided
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

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


// Query to get students with perfect attendance
$perfectAttendance_sql = "
    SELECT 
        s.srcode,
        s.studentID, 
        CONCAT(s.name) AS student_name, 
        c.grade_level, 
        c.section,
        COUNT(a.date) AS total_days,
        SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_days
    FROM 
        student s
    JOIN 
        classes c ON s.class_id = c.class_id
    JOIN 
        attendance a ON s.studentID = a.studentID
    WHERE 
        1=1
        $gradeCondition
        $dateCondition
    GROUP BY 
        s.studentID, s.name, c.grade_level, c.section
    HAVING 
        total_days = present_days
";


$result = $conn->query($perfectAttendance_sql);

// Check if there are any results and store them in an array
$perfectAttendanceStudents = [];
if ($result && $result->num_rows > 0) {
    $perfectAttendanceStudents = $result->fetch_all(MYSQLI_ASSOC);
}

// Query to get grade levels and sections
$sectionQuery = "SELECT grade_level, section FROM classes WHERE grade_level IN ($gradeList)";
$sectionResult = $conn->query($sectionQuery);

$gradeSections = [];
while ($row = $sectionResult->fetch_assoc()) {
    $gradeSections[$row['grade_level']][] = $row['section'];
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
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i"
        rel="stylesheet">

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
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Perfect Attendance</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Students with Perfect Attendance<br>

                                <!-- Filter Section -->
                            </h5>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="gradeFilter">Select Grade:</label>
                                    <select id="gradeFilter" class="form-select">
                                        <option value="">Select Grade</option>
                                        <?php foreach ($allowedGrades as $grade): ?>
                                            <option value="<?= $grade ?>"><?= $grade ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="sectionFilter">Select Section:</label>
                                    <select id="sectionFilter" class="form-select">
                                        <option value="">All Sections</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="monthPicker">Select Month:</label>
                                    <input type="month" id="monthPicker" class="form-control" name="month">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button id="filterButton" class="btn btn-primary w-100">Filter</button>
                                    <button id="clearButton" class="btn btn-secondary w-100 ms-2">Clear</button>
                                </div>
                            </div>

                            <!-- Table for displaying the students with perfect attendance -->
                            <table class="table table-bordered" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <th>Sr-Code</th>
                                        <th>Name</th>
                                        <th>Grade Level</th>
                                        <th>Section</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($perfectAttendanceStudents as $student): ?>
                                        <tr data-srcode="<?= htmlspecialchars($student['srcode']) ?>"
                                            data-grade="<?= htmlspecialchars($student['grade_level']) ?>"
                                            data-section="<?= htmlspecialchars($student['section']) ?>"
                                            data-date="<?= $startOfMonth ?>">
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
        </section>
    </main>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

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
        document.addEventListener('DOMContentLoaded', function () {
            const dataTable = new simpleDatatables.DataTable("#attendanceTable", {
                searchable: true,
                paging: true,
                perPage: 10,
            });

            // Initialize filter variables
            const gradeFilter = document.getElementById('gradeFilter');
            const sectionFilter = document.getElementById('sectionFilter');
            const monthPicker = document.getElementById('monthPicker');

            // Function to filter the table based on selected filters
            function applyFilters() {
                const gradeValue = gradeFilter.value.toUpperCase();
                const sectionValue = sectionFilter.value.toUpperCase();
                const monthValue = monthPicker.value;

                const rows = document.querySelectorAll('#attendanceTable tbody tr');

                rows.forEach(row => {
                    const grade = row.getAttribute('data-grade').toUpperCase();
                    const section = row.getAttribute('data-section').toUpperCase();
                    const rowDate = row.getAttribute('data-date');

                    let showRow = true;

                    // Apply grade filter
                    if (gradeValue && grade !== gradeValue) showRow = false;

                    // Apply section filter
                    if (sectionValue && section !== sectionValue) showRow = false;

                    // Apply month filter
                    if (monthValue) {
                        const [selectedYear, selectedMonth] = monthValue.split('-');
                        const selectedMonthStart = new Date(selectedYear, selectedMonth - 1, 1);
                        const selectedMonthEnd = new Date(selectedYear, selectedMonth, 0);
                        const attendanceDate = new Date(rowDate);

                        if (!(attendanceDate >= selectedMonthStart && attendanceDate <= selectedMonthEnd)) showRow = false;
                    }

                    // Show or hide the row based on filters
                    row.style.display = showRow ? '' : 'none';
                });
            }


            // Apply filters when the filter button is clicked
            document.getElementById('filterButton').addEventListener('click', applyFilters);

            // Reset filters and table content when the clear button is clicked
            document.getElementById('clearButton').addEventListener('click', function () {
                gradeFilter.value = '';
                sectionFilter.innerHTML = '<option value="">All Sections</option>';
                monthPicker.value = '';
                dataTable.search('');
                applyFilters();
            });

            // Update section dropdown based on selected grade
            gradeFilter.addEventListener('change', function () {
                const selectedGrade = this.value;
                const sections = gradeSections[selectedGrade] || [];
                sectionFilter.innerHTML = '<option value="">All Sections</option>';

                sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionFilter.appendChild(option);
                });
            });
        });
    </script>

</body>

</html>
