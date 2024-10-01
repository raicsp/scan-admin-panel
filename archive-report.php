<?php
include 'database/db_connect.php';
include 'database/db-report.php';

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
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
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
      <h1>ATTENDANCE REPORT</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Data</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">ATTENDANCE REPORT</h5>
              <form method="GET">
                <div class="form-row mb-3">
                  <div class="col-md-3">
                    <label for="gradeFilter" class="form-label"><b>Select Grade</b></label>
                    <select name="gradeFilter" id="gradeFilter" class="form-select" onchange="updateSections()">
                      <option value="">Select Grade</option>
                      <?php foreach ($allGrades as $grade) : ?>
                        <option value="<?= htmlspecialchars($grade) ?>" <?= $grade == $gradeFilter ? 'selected' : '' ?>><?= htmlspecialchars($grade) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label for="sectionFilter" class="form-label"><b>Select Section</b></label>
                    <select name="sectionFilter" id="sectionFilter" class="form-select">
                      <option value="">Select Section</option>
                      <?php
                      if ($gradeFilter && isset($allSectionsByGrade[$gradeFilter])) {
                        foreach ($allSectionsByGrade[$gradeFilter] as $section) : ?>
                          <option value="<?= htmlspecialchars($section) ?>" <?= $section == $sectionFilter ? 'selected' : '' ?>><?= htmlspecialchars($section) ?></option>
                      <?php endforeach;
                      } ?>
                    </select>
                  </div>

                </div>



                <!-- Second row: Initial Date, Ending Date -->
                <div class="form-row mb-3">
                  <div class="col-md-3">
                    <label for="startDate" class="form-label"><b>Initial Date</b></label>
                    <input type="date" id="startDate" class="form-control" name="startDate" value="<?= htmlspecialchars($startDate) ?>">
                  </div>
                  <div class="col-md-3">
                    <label for="endDate" class="form-label"><b>Ending Date</b></label>
                    <input type="date" id="endDate" class="form-control" name="endDate" value="<?= htmlspecialchars($endDate) ?>">
                  </div>
                </div>

                <!-- Buttons for Filtering and Exporting CSV -->
                <div class="row mb-3">
                  <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="submit" name="export" value="csv" class="btn btn-success">Generate CSV</button>
                  </div>
                </div>
              </form>

              <div class="table-responsive">
                <table class="table table-hover table-bordered" id="studentsTable">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <?php foreach ($dates as $date) : ?>
                        <th><?= htmlspecialchars($date) ?></th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($students as $student) : ?>
                      <tr>
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