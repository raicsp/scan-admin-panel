<?php

$activePage = 'add-student';
include 'database/db_connect.php';
include 'database/db-add-student.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Administrator | Laboratory School | Batangas State University TNEU</title>
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
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const gradeSelect = document.getElementById('inputGradeLevel');
            const sectionSelect = document.getElementById('inputSection');

            // Populate grade levels
            const grades = <?php echo json_encode($grades); ?>;
            grades.forEach(grade => {
                const option = document.createElement('option');
                option.value = grade;
                option.textContent = grade;
                gradeSelect.appendChild(option);
            });

            // Populate sections based on selected grade
            gradeSelect.addEventListener('change', function () {
                const selectedGrade = this.value;
                const sections = <?php echo json_encode($sections); ?>;

                // Clear current sections
                sectionSelect.innerHTML = '<option value="">Select Section</option>';

                if (selectedGrade && sections[selectedGrade]) {
                    sections[selectedGrade].forEach(section => {
                        const option = document.createElement('option');
                        option.value = section;
                        option.textContent = section;
                        sectionSelect.appendChild(option);
                    });
                }
            });
        });
    </script>

    <style>
        #alertContainer {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
        }

        .alert {
            margin: 0 auto;
            width: 90%;
            max-width: 600px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header .btn-primary {
            margin-left: auto;
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Student Registration</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Student Registration</li>
                </ol>
            </nav>
        </div>

        <div id="alertContainer" class="container mt-3">
            <?php if (isset($_SESSION['alertMessage']) && !empty($_SESSION['alertMessage'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        Swal.fire({
                            icon: '<?= $_SESSION['alertType'] ?>', // 'success', 'error', 'warning', 'info'
                            title: 'Success!',
                            text: '<?= $_SESSION['alertMessage'] ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
                <?php
                // Clear the session alert after displaying it
                unset($_SESSION['alertMessage']);
                unset($_SESSION['alertType']);
                ?>
            <?php endif; ?>
        </div>



        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">STUDENT INFORMATION FORM</h5>
                            <div class="row">
                                <label class="form-label"><strong>Choose Encoding Type: </strong></label>
                                <div class="col-md-6" style="text-align: center;">
                                    <input class="form-check-input" type="radio" name="encodingType"
                                        id="individualEncoding" value="individual" checked>
                                    <label class="form-check-label" for="individualEncoding">Individual Encoding</label>
                                </div>
                                <div class="col-md-6" style="text-align: center;">
                                    <input class="form-check-input" type="radio" name="encodingType" id="csvImport"
                                        value="csv">
                                    <label class="form-check-label" for="csvImport">CSV Import</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label></label>
                                </div>
                            </div>


                            <!-- Individual Encoding Form -->
                            <form id="individualForm" class="row g-3" method="POST" action="">
                                <div class="col-md-6">
                                    <label for="inputSrCode" class="form-label"><b>Sr-Code</b></label>
                                    <input type="text" name="srcode" class="form-control" id="inputSrCode"
                                        placeholder="21-12345" required maxlength="8" pattern="\d{2}-\d{5}"
                                        title="Enter a valid SR Code format: 2 digits, dash, and 5 numbers (e.g., 21-12345)">
                                </div>
                                <div class="col-md-6">
                                    <label for="inputName" class="form-label"><b>Full Name</b></label>
                                    <input type="text" name="student_name" class="form-control" id="inputName"
                                        placeholder="John Doe" required maxlength="50" pattern="[a-zA-Z\s]+"
                                        title="Only letters are allowed, up to 50 characters">
                                </div>
                                <div class="col-md-6">
                                    <label for="inputGender" class="form-label"><b>Gender</b></label>
                                    <select name="gender" id="inputGender" class="form-select" required>
                                        <option selected>Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="inputGradeLevel" class="form-label"><b>Grade Level</b></label>
                                    <select name="grade_level" id="inputGradeLevel" class="form-select" required>
                                        <option value="">Select Grade Level</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputSection" class="form-label"><b>Section</b></label>
                                    <select name="section" id="inputSection" class="form-select" required>
                                        <option value="">Select Section</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputParentName" class="form-label"><b>Parent Name</b></label>
                                    <input type="text" name="parent_name" class="form-control" id="parentName"
                                        placeholder="James Doe" required maxlength="50" pattern="[a-zA-Z\s]+"
                                        title="Only letters are allowed, up to 50 characters">
                                </div>
                                <div class="col-md-6">
                                    <label for="inputContact" class="form-label"><b>Parent's Contact</b></label>
                                    <input type="text" name="parent_contact" class="form-control" id="inputContact"
                                        placeholder="09123456789" required pattern="\d{11}" required maxlength="11"
                                        title="Enter exactly 11 digits">
                                </div>
                                <div class="col-md-6">
                                    <label for="inputEmail" class="form-label"><b>Parent's Email</b></label>
                                    <input type="email" name="parent_email" class="form-control" id="inputEmail"
                                        placeholder="example@gmail.com" required maxlength="50"
                                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                        title="Please enter a valid email address, up to 50 characters">
                                </div>
                                <!-- <div class="col-md-6">
                                    <label for="inputSchoolYear" class="form-label"><b>School Year</b></label>
                                    <input type="text" name="school_year" class="form-control" id="inputSchoolYear" placeholder="2023-2024" required>
                                </div> -->
                                <div class="text-center">
                                    <button type="submit" name="submit_individual"
                                        class="btn btn-primary">Submit</button>

                                </div>
                            </form>


 <!-- CSV Import Form -->
<form id="csvForm" class="row g-3 d-none" method="POST" action="" enctype="multipart/form-data">

<!-- Download CSV Template Section -->
<div class="col-md-12 mt-3">
    <h5><b>Download CSV Template</b></h5>
    <p>To ensure proper formatting, use the template below to add students:</p>
    <a href="student_template.csv" class="btn btn-secondary mb-3" download>Download Template (CSV)</a>
</div>

<!-- Horizontal line separator -->
<hr class="mt-0 mb-4">

<!-- Grade Level and Section Inputs -->
<div class="col-md-6">
    <label for="inputGradeLevelCSV" class="form-label"><b>Grade Level</b></label>
    <select name="grade_level" id="inputGradeLevelCSV" class="form-select" required>
        <option value="">Select Grade Level</option>
        <!-- Populate dynamically as needed -->
    </select>
</div>

<div class="col-md-6">
    <label for="inputSectionCSV" class="form-label"><b>Section</b></label>
    <select name="section" id="inputSectionCSV" class="form-select" required>
        <option value="">Select Section</option>
        <!-- Populate dynamically as needed -->
    </select>
</div>


<!-- File Upload Section -->
<div class="col-md-12">
    <label for="inputCSVFile" class="form-label"><b>Select CSV File to Upload</b></label>
    <input type="file" name="csv_file" class="form-control" id="inputCSVFile" accept=".csv" required>
    <small class="form-text text-muted">Only .csv files are supported.</small>
</div>

<!-- Submit Button -->
<div class="col-md-12 text-center mt-3">
    <button type="submit" name="import_csv" class="btn btn-primary">Import CSV</button>
</div>

</form>


                            


                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const individualEncodingRadio = document.getElementById('individualEncoding');
            const csvImportRadio = document.getElementById('csvImport');
            const individualForm = document.getElementById('individualForm');
            const csvForm = document.getElementById('csvForm');

            individualEncodingRadio.addEventListener('change', () => {
                individualForm.classList.remove('d-none');
                csvForm.classList.add('d-none');
            });

            csvImportRadio.addEventListener('change', () => {
                individualForm.classList.add('d-none');
                csvForm.classList.remove('d-none');
            });

            // Display alert message if set
            const alertMessage = <?= json_encode($alertMessage) ?>;
            const alertType = <?= json_encode($alertType) ?>;
            if (alertMessage) {
                Swal.fire({
                    icon: alertType,
                    title: alertType.charAt(0).toUpperCase() + alertType.slice(1), // Capitalize first letter
                    text: alertMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const gradeSelectCSV = document.getElementById('inputGradeLevelCSV');
            const sectionSelectCSV = document.getElementById('inputSectionCSV');

            const grades = <?php echo json_encode($grades); ?>;
            const sections = <?php echo json_encode($sections); ?>;

            // Populate grade levels for CSV import
            grades.forEach(grade => {
                const option = document.createElement('option');
                option.value = grade;
                option.textContent = grade;
                gradeSelectCSV.appendChild(option);
            });

            // Populate sections based on selected grade for CSV import
            gradeSelectCSV.addEventListener('change', function () {
                const selectedGrade = this.value;
                sectionSelectCSV.innerHTML = '<option value="">Select Section</option>';

                if (selectedGrade && sections[selectedGrade]) {
                    sections[selectedGrade].forEach(section => {
                        const option = document.createElement('option');
                        option.value = section;
                        option.textContent = section;
                        sectionSelectCSV.appendChild(option);
                    });
                }
            });
        });
    </script>




    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>