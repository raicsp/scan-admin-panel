<?php
include 'database/db_connect.php';
include 'database/db-teacher-attendance.php';
$activePage = 'attendance';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Faculty | Laboratory School | Batangas State University TNEU</title>
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
  </style>
</head>

<body>
  <?php include 'header.php'; ?>
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
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Attendance Overview</h5>
              <form method="GET">
                <div class="form-row mb-3">

                  <!-- <div class="col-md-3">
                    <label for="syFilter" class="form-label"><b>Select School Year</b></label>
                    <select name="syFilter" id="syFilter" class="form-select">
                      <option value="">Select School Year</option>
                      <?php
                      $schoolYears = array_unique(array_column($students, 'school_year'));
                      foreach ($schoolYears as $schoolYear) : ?>
                        <option value="<?= htmlspecialchars($schoolYear) ?>" <?= $schoolYear == $syFilter ? 'selected' : '' ?>><?= htmlspecialchars($schoolYear) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div> -->
                </div>



                <!-- Second row: Initial Date, Ending Date -->
                <div class="form-row align-items-end mb-3">
                  <div class="col-md-5">
                    <label for="startDate" class="form-label"><b>Initial Date</b></label>
                    <input type="date" id="startDate" class="form-control" name="startDate" value="<?= htmlspecialchars($startDate) ?>">
                  </div>
                  <div class="col-md-5">
                    <label for="endDate" class="form-label"><b>Ending Date</b></label>
                    <input type="date" id="endDate" class="form-control" name="endDate" value="<?= htmlspecialchars($endDate) ?>">
                  </div>
                  <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-50">Filter</button>
                  </div>
                </div>


              </form>

              <div class="table-responsive">
                <table class="table table-hover table-bordered" id="studentsTable">
                  <thead>
                    <tr>
                      <th>Sr-Code</th>
                      <th>Name</th>
                      <?php foreach ($dates as $date) : ?>
                        <th><?= htmlspecialchars($date) ?></th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($students as $student) : ?>
                      <tr>
                        <td><?= htmlspecialchars($student['srcode']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <?php foreach ($dates as $date) : ?>
                          <td><?= isset($student['data'][$date]) ? htmlspecialchars($student['data'][$date]) : 'Absent' ?></td>
                        <?php endforeach; ?>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
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
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dataTable = new simpleDatatables.DataTable("#studentsTable", {
        searchable: false,
        paging: true,
        fixedHeight: true,
        perPage: 10, // Set the number of rows per page
        labels: {
          placeholder: "Search...",
          perPage: "entries per page",
          noRows: "No results found",
          info: "Showing {start} to {end} of {rows} results"
        }
      });
    });
  </script>
</body>

</html>