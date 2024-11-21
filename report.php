<?php
include 'database/db_connect.php';
include 'database/db-report.php';
$activePage = 'attendance-report';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Administrator | Laboratory School | Batangas State University TNEU</title>
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
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .form-control,
        .form-select {
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
        }

        /* Custom form layout for spacing */
        .form-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .form-row .col-md-3 {
            flex-grow: 1;
        }

        /* Align buttons */
        .btn-container {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }

        /* Styling improvements for buttons */
        .btn-primary,
        .btn-success {
            padding: 0.4rem 1rem;
            font-size: 0.9rem;
        }

        /* Improve table styling */
        .table {
            margin-top: 20px;
        }

        /* Responsive form layout */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
        }

        /* Ensure buttons have consistent width */
        .btn-container .btn {
            width: 200px;
            /* Adjust this value as needed for your layout */
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Attendance Report</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance Report</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">School Form (SF2) Daily Attendance Report of Learners</h5>
                            <form method="GET" action="admin-daily-report.php">
                                <div class="form-row mb-3">
                                    <div class="col-md-3">
                                        <label for="monthFilter" class="form-label"><b>Select Month</b></label>
                                        <select name="monthFilter" id="monthFilter" class="form-select">
                                            <option value="">Select Month</option>
                                            <?php
                                            // List of months
                                            $months = [
                                                '01' => 'January',
                                                '02' => 'February',
                                                '03' => 'March',
                                                '04' => 'April',
                                                '05' => 'May',
                                                '06' => 'June',
                                                '07' => 'July',
                                                '08' => 'August',
                                                '09' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December'
                                            ];

                                            // Loop through the months and create the options
                                            foreach ($months as $monthNum => $monthName):
                                                ?>
                                                <option value="<?= $monthNum ?>" <?= $monthNum == $monthFilter ? 'selected' : '' ?>>
                                                    <?= $monthName ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="gradeFilter" class="form-label"><b>Select Grade</b></label>
                                        <select name="gradeFilter" id="gradeFilter" class="form-select"
                                            onchange="updateSections()">
                                            <option value="">Select Grade</option>
                                            <?php foreach ($allGrades as $grade): ?>
                                                <option value="<?= htmlspecialchars($grade) ?>" <?= $grade == $gradeFilter ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($grade) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="sectionFilter" class="form-label"><b>Select Section</b></label>
                                        <select name="sectionFilter" id="sectionFilter" class="form-select">
                                            <option value="">Select Section</option>
                                            <?php if ($gradeFilter && isset($allSectionsByGrade[$gradeFilter])):
                                                foreach ($allSectionsByGrade[$gradeFilter] as $section): ?>
                                                    <option value="<?= htmlspecialchars($section) ?>"
                                                        <?= $section == $sectionFilter ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($section) ?>
                                                    </option>
                                                <?php endforeach;
                                            endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Generate Daily Report</button>
                            </form>

                            <hr>

                            <h5 class="card-title">Monthly Attendance Report of Learners</h5>
                            <form method="GET" action="admin-monthly-report.php">
                                <div class="form-row mb-3">
                                    <div class="col-md-3">
                                        <label for="gradeFilterMonthly" class="form-label"><b>Select Grade</b></label>
                                        <select name="gradeFilterMonthly" id="gradeFilterMonthly" class="form-select"
                                            onchange="updateSectionsMonthly()">
                                            <option value="">Select Grade</option>
                                            <?php foreach ($allGrades as $grade): ?>
                                                <option value="<?= htmlspecialchars($grade) ?>"
                                                    <?= $grade == $gradeFilterMonthly ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($grade) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="sectionFilterMonthly" class="form-label"><b>Select
                                                Section</b></label>
                                        <select name="sectionFilterMonthly" id="sectionFilterMonthly"
                                            class="form-select">
                                            <option value="">Select Section</option>
                                            <?php if ($gradeFilterMonthly && isset($allSectionsByGrade[$gradeFilterMonthly])):
                                                foreach ($allSectionsByGrade[$gradeFilterMonthly] as $section): ?>
                                                    <option value="<?= htmlspecialchars($section) ?>"
                                                        <?= $section == $sectionFilterMonthly ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($section) ?>
                                                    </option>
                                                <?php endforeach;
                                            endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- <button type="submit" class="btn btn-secondary" name="filterMonthly">Filter</button> -->
                                <button type="submit" class="btn btn-primary">Generate Monthly Report</button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>


    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i
            class="bi bi-arrow-up-short"></i></a>
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        function updateSections() {
            const gradeFilter = document.getElementById('gradeFilter').value;
            const sectionFilter = document.getElementById('sectionFilter');
            sectionFilter.innerHTML = '<option value="">Select Section</option>';

            const sectionsByGrade = <?php echo json_encode($allSectionsByGrade); ?>;
            if (sectionsByGrade[gradeFilter]) {
                sectionsByGrade[gradeFilter].forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionFilter.appendChild(option);
                });
            }
        }

        function updateSectionsMonthly() {
            const gradeFilterMonthly = document.getElementById('gradeFilterMonthly').value;
            const sectionFilterMonthly = document.getElementById('sectionFilterMonthly');
            sectionFilterMonthly.innerHTML = '<option value="">Select Section</option>';

            const sectionsByGrade = <?php echo json_encode($allSectionsByGrade); ?>;
            if (sectionsByGrade[gradeFilterMonthly]) {
                sectionsByGrade[gradeFilterMonthly].forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionFilterMonthly.appendChild(option);
                });
            }
        }
    </script>
    <script>
        function toggleFilters() {
            const reportType = document.getElementById('reportType').value;
            document.getElementById('dailyFilters').style.display = reportType === 'daily' ? 'block' : 'none';
        }
    </script>
    <script>
        function updateSections() {
            const gradeFilter = document.getElementById('gradeFilter').value;
            const sectionFilter = document.getElementById('sectionFilter');
            sectionFilter.innerHTML = '<option value="">Select Section</option>';

            const sectionsByGrade = <?php echo json_encode($allSectionsByGrade); ?>;
            if (sectionsByGrade[gradeFilter]) {
                sectionsByGrade[gradeFilter].forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionFilter.appendChild(option);
                });
            }
        }

        function updateSectionsMonthly() {
            const gradeFilterMonthly = document.getElementById('gradeFilterMonthly').value;
            const sectionFilterMonthly = document.getElementById('sectionFilterMonthly');
            sectionFilterMonthly.innerHTML = '<option value="">Select Section</option>';

            const sectionsByGrade = <?php echo json_encode($allSectionsByGrade); ?>;
            if (sectionsByGrade[gradeFilterMonthly]) {
                sectionsByGrade[gradeFilterMonthly].forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionFilterMonthly.appendChild(option);
                });
            }
        }

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (event) {
                Swal.fire({
                    title: 'Generating Report...',
                    text: 'Please wait while the report is being generated.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading(); // Show loading spinner
                    }
                });

                // Simulate an API request or file generation here (example)
                setTimeout(function () {
                    // When file generation is completed
                    Swal.close();  // Close the loading spinner

                    // Trigger download or redirect
                    window.location.href = 'report.php'; // Or use anchor link <a href="...">download</a>
                }, 3000);  // Simulate a 3-second delay for generation
            });
        });

    </script>

</body>

</html>
