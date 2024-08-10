<?php
include 'database/db_connect.php';
include 'database/db-attendance.php';

$activePage = 'attendance';
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
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

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
      <h1>Student Attendance</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Data</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Student Attendance <span>| <?= date("F j, Y") ?></span> </h5>
              <!-- Filter-->
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
                    <?php
                    $sections = array_unique(array_column($students, 'section'));
                    foreach ($sections as $section) : ?>
                      <option value="<?= htmlspecialchars($section) ?>"><?= htmlspecialchars($section) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            
              <!-- Table with stripped rows -->
              <table class="table datatable" id="studentsTable">
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
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->

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
  <!-- Filter script -->
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const gradeFilter = document.getElementById('gradeFilter');
      const sectionFilter = document.getElementById('sectionFilter');
      const table = document.getElementById('studentsTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      const filterTable = () => {
        const gradeValue = gradeFilter.value;
        const sectionValue = sectionFilter.value;

        for (let row of rows) {
          const grade = row.cells[2].textContent;
          const section = row.cells[3].textContent;

          if ((gradeValue === "" || grade === gradeValue) && (sectionValue === "" || section === sectionValue)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      };

      gradeFilter.addEventListener('change', filterTable);
      sectionFilter.addEventListener('change', filterTable);
    });
  </script>

</body>

</html>