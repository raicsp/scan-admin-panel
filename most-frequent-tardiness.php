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
}

// Construct grade condition for the SQL query
$gradeCondition = '';
if (!empty($allowedGrades)) {
    $gradeList = "'" . implode("', '", $allowedGrades) . "'";
    $gradeCondition = "AND c.grade_level IN ($gradeList)";
}

// Query to get students with most absences
$late_sql = "
SELECT s.studentID, CONCAT(s.name) AS student_name, 
             c.grade_level, c.section, COUNT(a.status) AS late_count, 
             ROUND((COUNT(a.status) / (SELECT COUNT(*) FROM attendance WHERE studentID = s.studentID)) * 100, 2) AS percentage
             FROM attendance a
             JOIN student s ON a.studentID = s.studentID
             JOIN classes c ON s.class_id = c.class_id
             WHERE a.status = 'Late'
             $gradeCondition
             GROUP BY s.studentID
             ORDER BY late_count DESC
";

$result = $conn->query($late_sql);

// Check if there are any results and store them in an array
$absentStudents = [];
if ($result && $result->num_rows > 0) {
    $absentStudents = $result->fetch_all(MYSQLI_ASSOC);
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

</head>

<body>

    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Students with Most Frequent Tardiness</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Most Frequent Tardiness</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Students with Most Frequent Tardiness<br> 
                                <span><?= date("F j, Y") ?></span>
                            </h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <select id="gradeFilter" class="form-select">
                                        <option value="">Select Grade</option>
                                        <?php foreach ($allowedGrades as $grade): ?>
                                            <option value="<?= $grade ?>"><?= $grade ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="sectionFilter" class="form-select">
                                        <option value="">All Sections</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" id="searchBar" class="form-control" placeholder="Search by Name">
                                </div>
                            </div>

                            <!-- Table for displaying the students with the most absences -->
                            <table class="table table-bordered" id="absenceTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Grade Level</th>
                                        <th>Section</th>
                                        <th>Tardiness</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($absentStudents as $student) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['student_name']) ?></td>
                                            <td><?= htmlspecialchars($student['grade_level']) ?></td>
                                            <td><?= htmlspecialchars($student['section']) ?></td>
                                            <td><?= htmlspecialchars($student['late_count']) ?></td>
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
        searchable: false, // Disable the default search input
        paging: true,
        fixedHeight: true,
        perPage: 10,
        labels: {
            placeholder: "Search...",
            perPage: "entries per page",
            noRows: "No results found",
            info: "Showing {start} to {end} of {rows} results"
        }
    });

    function filterTable() {
        const gradeFilter = document.getElementById('gradeFilter').value.toUpperCase();
        const sectionFilter = document.getElementById('sectionFilter').value.toUpperCase();
        const searchQuery = document.getElementById('searchBar').value.toUpperCase();

        // Build filter query based on selected values
        let filterQuery = '';

        // Append grade filter
        if (gradeFilter) {
            filterQuery += gradeFilter;
        }

        // Append section filter
        if (sectionFilter) {
            filterQuery += ' ' + sectionFilter; // Add space to separate terms
        }

        // Append search query for name
        if (searchQuery) {
            filterQuery += ' ' + searchQuery; // Add space to separate terms
        }

        // Apply the search/filter on the datatable
        dataTable.search(filterQuery.trim()); // Use search method to filter the table
    }

    document.getElementById('gradeFilter').addEventListener('change', function() {
        const selectedGrade = this.value;

        // Filter sections based on selected grade
        const sections = gradeSections[selectedGrade] || [];
        const sectionDropdown = document.getElementById('sectionFilter');
        sectionDropdown.innerHTML = '<option value="">All Sections</option>';

        sections.forEach(section => {
            const option = document.createElement('option');
            option.value = section;
            option.textContent = section;
            sectionDropdown.appendChild(option);
        });

        // Trigger table filtering after updating the section dropdown
        filterTable();
    });

  //  document.getElementById('sectionFilter').addEventListener('change', filterTables);
    document.getElementById('searchBar').addEventListener('input', filterTable);
});
</script>
