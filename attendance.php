<?php
include 'database/db_connect.php';
include 'database/db-attendance.php';

$activePage = 'attendance';
$gradeSections = [];

// Create a mapping of grades to sections
foreach ($students as $student) {
    $grade = $student['grade_level'];
    $section = $student['section'];

    if (!isset($gradeSections[$grade])) {
        $gradeSections[$grade] = [];
    }

    if (!in_array($section, $gradeSections[$grade])) {
        $gradeSections[$grade][] = $section;
    }
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
            <h1>Student Attendance</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Student Attendance</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Student Attendance<br><span><?= date("F j, Y") ?></span></h5>

                            <!-- Filter -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select id="gradeFilter" class="form-select">
                                        <option value="">Select Grade</option>
                                        <?php
                                        $grades = array_unique(array_column($students, 'grade_level'));
                                        foreach ($grades as $grade) : ?>
                                            <option value="<?= htmlspecialchars($grade) ?>"><?= htmlspecialchars($grade) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select id="sectionFilter" class="form-select">
                                        <option value="">Select Section</option>
                                        <!-- Sections will be dynamically added based on the selected grade -->
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="row mb-3">
                                <div class="col-md-12">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name">
                                </div>
                            </div> -->

                            <!-- Table -->
                            <table class="table table-hover" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Attendance Status</th>
                                        <th>Grade Level</th>
                                        <th>Section</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['name']) ?></td>
                                            <td><?= htmlspecialchars($student['status']) ?></td>
                                            <td><?= htmlspecialchars($student['grade_level']) ?></td>
                                            <td><?= htmlspecialchars($student['section']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- End Table -->

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <!-- Back to Top -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const gradeSections = <?= json_encode($gradeSections) ?>;
        const gradeFilter = document.getElementById('gradeFilter');
        const sectionFilter = document.getElementById('sectionFilter');
        // const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('studentsTable');

        // Initialize DataTable
        const dataTable = new simpleDatatables.DataTable("#studentsTable", {
            searchable: true, // Disable default search input
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

        // Update section options based on selected grade
        gradeFilter.addEventListener('change', () => {
            const selectedGrade = gradeFilter.value;
            sectionFilter.innerHTML = '<option value="">Select Section</option>'; // Clear previous sections

            if (selectedGrade && gradeSections[selectedGrade]) {
                gradeSections[selectedGrade].forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionFilter.appendChild(option);
                });
            }

            filterTable(); // Apply filter after changing grade
        });

        // Filter table based on search and selection
        function filterTable() {
            const gradeFilterValue = gradeFilter.value.toUpperCase();
            const sectionFilterValue = sectionFilter.value.toUpperCase();
          //  const searchQuery = searchInput.value.toUpperCase();

            // Build filter query based on selected values
            let filterQuery = '';

            // Append grade filter
            if (gradeFilterValue) {
                filterQuery += gradeFilterValue;
            }

            // Append section filter
            if (sectionFilterValue) {
                filterQuery += ' ' + sectionFilterValue; // Add space to separate terms
            }

            // Append search query for name
            // if (searchQuery) {
            //     filterQuery += ' ' + searchQuery; // Add space to separate terms
            // }

            // Apply the search/filter on the datatable
            dataTable.search(filterQuery.trim()); // Use search method to filter the table
        }

        // Event listeners for filtering
        gradeFilter.addEventListener('change', filterTable);
    //    sectionFilter.addEventListener('change', filterTable);
     //   searchInput.addEventListener('input', filterTable);
    });
</script>


</body>

</html>
