<?php
include 'database/db_connect.php';
include 'database/db-class-students.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Administrator | Laboratory School | Batangas State University TNEU</title>
  <!-- Include your CSS and JS files here -->
  <!-- Favicons -->
  <link href="assets/img/bsu.png" rel="icon">
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
</head>

<body>

  <!-- Header and Sidebar -->
  <?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Student List</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="class-management.php">Class Management</a></li>
          <li class="breadcrumb-item active">Student List</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                Student List
                <br>
                <?php if ($classDetails) : ?>
                  <small class="text-muted">
                    <?= htmlspecialchars($classDetails['grade_level']) ?> - <?= htmlspecialchars($classDetails['section']) ?>
                  </small>
                <?php endif; ?>
              </h5>
              <div class="d-flex justify-content-start">
              <span class="text-muted" style="padding-right: 30px;">
                <?= count($students); ?> Students
              </span>
            </div>

            </div>
    
            <div class="card-body">
              <!-- Table with student list -->
              <!-- Table with student list -->
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th scope="col">#</th> <!-- Add a column header for the numbering -->
                    <th scope="col">Student Name</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($students)) : ?>
                    <?php $counter = 1; ?> <!-- Initialize a counter -->
                    <?php foreach ($students as $student) : ?>
                      <tr onclick="window.location.href='student-details.php?name=<?= urlencode($student['name']) ?>';" style="cursor: pointer;">
                        <td><?= $counter++; ?></td> <!-- Display the counter and increment it -->
                        <td><?= htmlspecialchars($student['name']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <tr>
                      <td colspan="2">No students found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>