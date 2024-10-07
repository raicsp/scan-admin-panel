<?php
include 'database/db_connect.php';
include 'database/db-view-archive.php';

$activePage = 'manage-years';

// Get the academic year from the URL
$academicYear = isset($_GET['academic_year']) ? urldecode($_GET['academic_year']) : '';

// Fetch archived student data based on the passed academic year
$archivedStudents = getArchivedStudents($conn, $academicYear, $gradeCondition);  // Assume this function takes academic year as a parameter
$grades = getDistinctGrades($conn, $gradeCondition);

// Prepare to store sections for each grade
$sectionsByGrade = [];
foreach ($grades as $grade) {
    $sectionsByGrade[$grade] = getSectionsByGrade($conn, $grade);
}
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
            <h1>VIEW ARCHIVED STUDENTS</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage-years.php">Manage Academic Years</a></li>
                    <li class="breadcrumb-item active">View Archived Students</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">ARCHIVED STUDENT DATA <br> <span><?= date("F j, Y") ?></span></h5>

                            <!-- Display the academic year passed from the previous page -->
                            <div class="mb-3">
                                <h6>Academic Year: <b><?= htmlspecialchars($academicYear) ?></b></h6>
                            </div>

                            <!-- Combo boxes for filtering -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select id="gradeFilter" class="form-control">
                                        <option value="">Select Grade</option>
                                        <?php foreach ($grades as $grade) : ?>
                                            <option value="<?= htmlspecialchars($grade) ?>"><?= htmlspecialchars($grade) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
        <select id="sectionFilter" class="form-control">
            <option value="">Select Section</option>
            <?php
            // Populate sections based on grades fetched
            foreach ($grades as $grade) :
                $sections = $sectionsByGrade[$grade];
                foreach ($sections as $section) : ?>
                    <option value="<?= htmlspecialchars($section) ?>" data-grade="<?= htmlspecialchars($grade) ?>"><?= htmlspecialchars($section) ?></option>
                <?php endforeach;
            endforeach; ?>
        </select>
    </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <form id="generateReportForm" action="archive-report.php" method="GET">
                                        <input type="hidden" name="academic_year" value="<?= htmlspecialchars($academicYear) ?>">
                                        <!-- Directly submit the form with the selected filters -->
                                        <button type="submit" class="btn btn-primary">Generate Report</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Table to display archived student data -->
                            <table class="table table-bordered" id="archiveTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Teacher Name</th>
                                        <th>Grade Level</th>
                                        <th>Section</th>
                                        <th>Academic Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($archivedStudents as $student) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['name']) ?></td>
                                            <td><?= htmlspecialchars($student['teacher_name']) ?></td>
                                            <td><?= htmlspecialchars($student['class_grade']) ?></td>
                                            <td><?= htmlspecialchars($student['class_section']) ?></td>
                                            <td><?= htmlspecialchars($student['school_year']) ?></td>
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
    const dataTable = new simpleDatatables.DataTable("#archiveTable", {
        searchable: false,
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
        const rows = document.querySelectorAll('#archiveTable tbody tr');

        rows.forEach(row => {
            const grade = row.cells[2].textContent.toUpperCase();
            const section = row.cells[3].textContent.toUpperCase();

            const gradeMatch = gradeFilter === '' || grade.includes(gradeFilter);
            const sectionMatch = sectionFilter === '' || section.includes(sectionFilter);

            row.style.display = gradeMatch && sectionMatch ? '' : 'none';
        });
    }

    // Fetch sections based on selected grade
    document.getElementById('gradeFilter').addEventListener('change', function() {
        const selectedGrade = this.value;

        // Make AJAX request to fetch sections for the selected grade
        $.ajax({
            type: "POST",
            url: "database/db-view-archive.php",
            data: { grade: selectedGrade },
            dataType: "json",
            success: function(response) {
                const sectionDropdown = document.getElementById('sectionFilter');
                sectionDropdown.innerHTML = '<option value="">Select Section</option>'; // Clear previous options

                // Populate the dropdown with sections
                response.forEach(function(section) {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionDropdown.appendChild(option);
                });

                filterTable(); // Re-filter table based on new sections
            },
            error: function() {
                console.error("Error fetching sections.");
            }
        });
    });

    document.getElementById('sectionFilter').addEventListener('change', filterTable);
});


    </script>
    <script>
document.getElementById('gradeFilter').addEventListener('change', function() {
    const selectedGrade = this.value;
    const sectionOptions = document.querySelectorAll('#sectionFilter option[data-grade]');
    
    // Show or hide sections based on the selected grade
    sectionOptions.forEach(option => {
        if (option.getAttribute('data-grade') === selectedGrade || selectedGrade === "") {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    // Reset section selection
    document.getElementById('sectionFilter').value = '';
});
</script>


</body>

</html>
