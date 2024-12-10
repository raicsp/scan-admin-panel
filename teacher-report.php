<?php
include 'database/db_connect.php';
include 'database/db-teacher-report.php';
$activePage = 'attendance-report';
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



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
      <h1>Attendance Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="teacher-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Attendance Report</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title" style="text-align: center;">School Form (SF2) Daily Attendance Report of Learners</h5>

              <!-- Daily Attendance Report Section -->
              <div class="mb-4" style="text-align: center;">
                <form method="GET" action="daily-report.php">
                  <div class="form-row justify-content-center">
                    <!-- Centered Select Month -->
                    <div class="col-md-4">
                      <label for="month" class="form-label"><b>Select Month</b></label>
                      <select name="month" id="month" class="form-select">
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                      </select>
                    </div>
                  </div>

                  <!-- Centered Button -->
                  <div class="form-row justify-content-center mt-3">
                    <div class="col-md-4">
                      <button type="submit" class="btn btn-primary btn-block" id="generateDailyReport">Generate Daily Report</button>
                    </div>
                  </div>
                </form>
              </div>

              <!-- Divider Line -->
              <hr class="my-4">

              <!-- Monthly Attendance Report Section -->
              <div class="mb-4" style="text-align: center;">
                <div class="col-md-12">
                  <h5 class="card-title" style="text-align: center;">Monthly Attendance Report of Learners</h5>
                </div>
                <form method="POST" action="monthly-report.php">
                  <button type="submit" name="export" value="csv" class="btn btn-primary" id="generateMonthlyReport">Generate Monthly
                    Report</button>
                </form>
              </div>

              <hr class="my-4">

              <!-- Date Range Filter for the Attendance Table -->
              <!-- <form method="GET">
                <div class="form-row mb-3">
                  <div class="col-md-3">
                    <label for="startDate" class="form-label"><b>Initial Date</b></label>
                    <input type="date" id="startDate" class="form-control" name="startDate"
                      value="<?= htmlspecialchars($startDate) ?>">
                  </div>
                  <div class="col-md-3">
                    <label for="endDate" class="form-label"><b>Ending Date</b></label>
                    <input type="date" id="endDate" class="form-control" name="endDate"
                      value="<?= htmlspecialchars($endDate) ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                  </div>
                </div>
              </form> -->

              <!-- Attendance Table -->
              <!-- <div class="table-responsive">
                <table class="table table-hover table-bordered" id="studentsTable">
                  <thead>
                    <tr>
                      <th>Sr-Code</th>
                      <th>Name</th>
                      <?php foreach ($dates as $date): ?>
                        <th><?= htmlspecialchars($date) ?></th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($students as $student): ?>
                      <tr>
                        <td><?= htmlspecialchars($student['srcode']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <?php foreach ($dates as $date): ?>
                          <td><?= isset($student['data'][$date]) ? htmlspecialchars($student['data'][$date]) : 'Absent' ?>
                          </td>
                        <?php endforeach; ?>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div> -->

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
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
    document.addEventListener("DOMContentLoaded", function() {
      const dataTable = new simpleDatatables.DataTable("#studentsTable", {
        searchable: true,
        paging: true,
        fixedHeight: true,
        perPage: 10,
        labels: {
          placeholder: "Search...",
          perPage: "entries per page",
          noRows: "No results found",
          info: "Showing {start} to {end} of {rows} results",
        },
      });
    });

    const updateSections = (gradeId, sectionId) => {
      const gradeFilter = document.getElementById(gradeId).value;
      const sectionFilter = document.getElementById(sectionId);
      sectionFilter.innerHTML = '<option value="">Select Section</option>';

      const sectionsByGrade = <?php echo json_encode($allSectionsByGrade); ?>;
      if (sectionsByGrade[gradeFilter]) {
        sectionsByGrade[gradeFilter].forEach((section) => {
          const option = document.createElement("option");
          option.value = section;
          option.textContent = section;
          sectionFilter.appendChild(option);
        });
      }
    };

    // Event listener for Daily Report generation
    document.getElementById("generateDailyReport").addEventListener("click", function(event) {
      event.preventDefault();
      const month = document.getElementById("month").value;

      if (!month) {
        Swal.fire({
          icon: "warning",
          title: "Incomplete Selection",
          text: "You need to select a month before generating the report.",
          confirmButtonText: "OK",
        });
      } else {
        Swal.fire({
          title: "Generate Daily Report?",
          text: "Are you sure you want to generate the daily report for the selected month?",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Yes, Generate",
          cancelButtonText: "Cancel",
        }).then((result) => {
          if (result.isConfirmed) {
            // Show 'Please wait...' message
            Swal.fire({
              title: "Generating Report...",
              text: "Please wait while the report is being prepared.",
              icon: "info",
              allowOutsideClick: false,
              showConfirmButton: false,
              didOpen: () => {
                Swal.showLoading(); // Show loading spinner
              },
            });

            // Create a form and submit it after delay
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = 'daily-report.php';

            // Add the month filter as a hidden input
            const monthInput = document.createElement('input');
            monthInput.type = 'hidden';
            monthInput.name = 'month';
            monthInput.value = month;
            form.appendChild(monthInput);

            document.body.appendChild(form);
            form.submit();

            // Close loading spinner after delay
            setTimeout(() => {
              Swal.close();
            }, 1000); // Simulated delay for generation
          }
        });
      }
    });

    // Event listener for Monthly Report generation
    document.getElementById("generateMonthlyReport").addEventListener("click", function(event) {
      event.preventDefault();

      Swal.fire({
        title: "Generate Monthly Report?",
        text: "Are you sure you want to generate the monthly report?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Generate",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          // Show 'Please wait...' message
          Swal.fire({
            title: "Generating Report...",
            text: "Please wait while the report is being prepared.",
            icon: "info",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading(); // Show loading spinner
            },
          });

          // Create a form and submit it after delay
          const form = document.createElement('form');
          form.method = 'GET';
          form.action = 'monthly-report.php';

          document.body.appendChild(form);
          form.submit();

          // Close loading spinner after delay
          setTimeout(() => {
            Swal.close();
          }, 1000); // Simulated delay for generation
        }
      });
    });
  </script>

</body>

</html>
