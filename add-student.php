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

  <title>SCAN</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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
      gradeSelect.addEventListener('change', function() {
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

  <!-- Custom CSS for Alert and Button Layout -->
  <style>
    #alertContainer {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1050;
      /* Ensure it appears above other content */
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
      /* Align the button to the right */
    }
  </style>

</head>

<body>

  <!-- ======= Header ======= -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidebar.php'; ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Student Information</h1>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="container mt-3">
      <?php if (!empty($alertMessage)) : ?>
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
          <?= $alertMessage ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Student Information Form</h5>
              <form class="row g-3" method="POST" action="">
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
                    <option value="">Select Grade</option>
                    <!-- Options will be populated by JavaScript -->
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="inputSection" class="form-label">Section</label>
                  <select name="section" id="inputSection" class="form-select" required>
                    <option value="">Select Section</option>
                    <!-- Options will be populated based on selected grade -->
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="inputParentContact" class="form-label">Parent Contact Number</label>
                  <input type="tel" name="parent_contact" class="form-control" id="inputParentContact" placeholder="123-456-7890" required>
                </div>
                <div class="col-md-6">
                  <label for="inputParentEmail" class="form-label">Parent Email Address</label>
                  <input type="email" name="parent_email" class="form-control" id="inputParentEmail" placeholder="parent@example.com" required>
                </div>
                <div class="col-md-6">
                  <label for="inputSchoolYear" class="form-label">School Year</label>
                  <input type="text" name="school_year" class="form-control" id="inputSchoolYear" placeholder="2024-2025" required>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck" required>
                    <label class="form-check-label" for="gridCheck">Confirm information is accurate</label>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form><!-- End Multi Columns Form -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

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

</body>

</html>