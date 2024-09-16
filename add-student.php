<?php
session_start();
$activePage = 'add-student';
include 'database/db_connect.php';
include 'database/db-add-student.php';

$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['import_csv'])) {
        // CSV Import Logic
        $fileName = $_FILES['csv_file']['tmp_name'];
        if ($_FILES['csv_file']['size'] > 0) {
            $file = fopen($fileName, 'r');
            fgetcsv($file); // Skip header row if your CSV has one

            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                $name = mysqli_real_escape_string($conn, $column[0]);
                $gender = mysqli_real_escape_string($conn, $column[1]);
                $profile_pic = mysqli_real_escape_string($conn, $column[2]); // Assuming this is now included
                $teacher_Id = mysqli_real_escape_string($conn, $column[3]); // Make sure teacher_Id is correct
                $gmail = mysqli_real_escape_string($conn, $column[4]);
                $class_id = mysqli_real_escape_string($conn, $column[5]);
                $parent_contact = mysqli_real_escape_string($conn, $column[6]);
                $school_year = mysqli_real_escape_string($conn, $column[7]);

                // Check if the student already exists
                $checkSql = "SELECT * FROM student WHERE name = '$name' AND class_id = '$class_id' AND school_year = '$school_year'";
                $result = mysqli_query($conn, $checkSql);

                if (mysqli_num_rows($result) == 0) {
                    // Insert student into the database
                    $sql = "INSERT INTO student (name, gender, profile_pic, teacher_Id, gmail, class_id, parent_contact, school_year) 
                            VALUES ('$name', '$gender', '$profile_pic', '$teacher_Id', '$gmail', '$class_id', '$parent_contact', '$school_year')";
                    if (!mysqli_query($conn, $sql)) {
                        $_SESSION['alertMessage'] = "Error: " . mysqli_error($conn);
                        $_SESSION['alertType'] = "danger";
                    }
                }
            }
            fclose($file);
            $_SESSION['alertMessage'] = "CSV Import successful.";
            $_SESSION['alertType'] = "success";
        } else {
            $_SESSION['alertMessage'] = "CSV file is empty.";
            $_SESSION['alertType'] = "danger";
        }
    } elseif (isset($_POST['student_name'])) {
        // Individual Encoding Logic
        $name = $_POST['student_name'];
        $gender = $_POST['gender'];
        $class_id = $_POST['grade_level'] . '-' . $_POST['section'];
        $parent_contact = $_POST['parent_contact'];
        $gmail = $_POST['parent_email'];
        $school_year = $_POST['school_year'];

        // Check if the student already exists
        $checkSql = "SELECT * FROM student WHERE name = '$name' AND class_id = '$class_id' AND school_year = '$school_year'";
        $result = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($result) == 0) {
            // Insert student into the database
            $sql = "INSERT INTO student (name, gender, class_id, parent_contact, gmail, school_year, teacher_Id) 
                    VALUES ('$name', '$gender', '$class_id', '$parent_contact', '$gmail', '$school_year', NULL)";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['alertMessage'] = "Student added successfully.";
                $_SESSION['alertType'] = "success";
            } else {
                $_SESSION['alertMessage'] = "Error: " . mysqli_error($conn);
                $_SESSION['alertType'] = "danger";
            }
        } else {
            $_SESSION['alertMessage'] = "Student already exists!";
            $_SESSION['alertType'] = "warning";
        }
    }

    // Redirect to prevent form resubmission on reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SCAN</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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
            <h1>Student Information</h1>
        </div>

        <div id="alertContainer" class="container mt-3">
    <?php if (isset($_SESSION['alertMessage']) && !empty($_SESSION['alertMessage'])): ?>
        <div class="alert alert-<?= $_SESSION['alertType'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['alertMessage'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
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
                            <h5 class="card-title">Student Information Form</h5>

                            <div class="mb-4">
                                <label class="form-label">Choose Encoding Type</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="encodingType" id="individualEncoding" value="individual" checked>
                                    <label class="form-check-label" for="individualEncoding">Individual Encoding</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="encodingType" id="csvImport" value="csv">
                                    <label class="form-check-label" for="csvImport">CSV Import</label>
                                </div>
                            </div>

                            <!-- Individual Encoding Form -->
                            <form id="individualForm" class="row g-3" method="POST" action="">
                                <div class="col-md-12">
                                    <label for="inputName" class="form-label">Full Name</label>
                                    <input type="text" name="student_name" class="form-control" id="inputName" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputGender" class="form-label">Gender</label>
                                    <select name="gender" id="inputGender" class="form-select" required>
                                        <option selected>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputGradeLevel" class="form-label">Grade Level</label>
                                    <select name="grade_level" id="inputGradeLevel" class="form-select" required>
                                        <option value="">Select Grade Level</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputSection" class="form-label">Section</label>
                                    <select name="section" id="inputSection" class="form-select" required>
                                        <option value="">Select Section</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputContact" class="form-label">Parent's Contact</label>
                                    <input type="text" name="parent_contact" class="form-control" id="inputContact" placeholder="0912 345 6789" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputEmail" class="form-label">Parent's Email</label>
                                    <input type="email" name="parent_email" class="form-control" id="inputEmail" placeholder="example@gmail.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputSchoolYear" class="form-label">School Year</label>
                                    <input type="text" name="school_year" class="form-control" id="inputSchoolYear" placeholder="2023-2024" required>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>

                            <!-- CSV Import Form -->
                            <form id="csvForm" class="row g-3 d-none" method="POST" action="" enctype="multipart/form-data">
                                
                                <!-- CSV format instruction -->
                                <div class="col-md-12 mt-3">
                                    <h5>CSV Format Instructions</h5>
                                    <p>Ensure that your CSV file follows the exact format below:</p>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Gender</th>
                                                <th>Teacher ID</th>
                                                <th>Gmail</th>
                                                <th>Class ID</th>
                                                <th>Parent Contact</th>
                                                <th>School Year</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>John Doe</td>
                                                <td>Male</td>
                                                <td>10</td>
                                                <td>john.doe@gmail.com</td>
                                                <td>4</td>
                                                <td>09123456789</td>
                                                <td>2024-2025</td>
                                            </tr>
                                            <tr>
                                                <td>Jane Smith</td>
                                                <td>Female</td>
                                                <td>10</td>
                                                <td>jane.smith@gmail.com</td>
                                                <td>4</td>
                                                <td>09123456789</td>
                                                <td>2024-2025</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12">
                                    <label for="inputCSVFile" class="form-label">CSV File</label>
                                    <input type="file" name="csv_file" class="form-control" id="inputCSVFile" accept=".csv" required>
                                </div>

                                <div class="text-center">
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
    });
</script>


    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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
