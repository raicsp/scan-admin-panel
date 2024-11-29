<?php
$activePage = 'dashboard';
include 'database/db_connect.php'; // Include the database connection
include 'database/db-teacher-dashboard.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Faculty | Laboratory School | Batangas State University TNEU</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

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

  <!-- Chart Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .equal-height {
      display: flex;
      flex-direction: column;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidebar.php'; ?>
  <!-- End Sidebar-->

  <!-- ======= Main ======= -->
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- 1st Layer: 3 Cards -->
        <div class="col-12">
          <div class="row">


            <div class="col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Present Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="present-today"><?php echo $present_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Present Today Card -->

            <div class="col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Absent Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-x-circle"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="absent-today"><?php echo $absent_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Absent Today Card -->
            <div class="col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Late Arrivals Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-clock"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="late-today"><?php echo $late_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Students Card -->
            <div class="col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Total Number of Students</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="student"><?php echo $student; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Late Students Card -->
          </div>
        </div><!-- End 1st Layer -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Gender Distribution</h5>
              <div id="genderChart" style="width: 100%; height: 400px;"></div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Attendance Status Overview</h5>
              <div id="attendancePieChart" style="width: 100%; height: 400px;"></div> <!-- Div for ApexCharts -->
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs" id="attendanceTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <!-- Daily Attendance Tab as active -->
                  <a class="nav-link active" id="daily-tab" data-bs-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="true">Daily Attendance</a>
                </li>
                <li class="nav-item" role="presentation">
                  <!-- Monthly Attendance Tab -->
                  <a class="nav-link" id="monthly-tab" data-bs-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="false">Monthly Attendance</a>
                </li>
              </ul>

              <!-- Tab content -->
              <div class="tab-content mt-3" id="attendanceTabsContent">
                <!-- Daily Attendance Tab -->
                <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                  <h5 class="card-title">Daily Attendance Trend</h5>
                  <div id="dailyTrendChart"></div>
                </div>

                <!-- Monthly Attendance Tab -->
                <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                  <h5 class="card-title">Monthly Attendance Trend</h5>
                  <div id="attendanceTrendChart"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="row">
            <div class="col-md-6 equal-height">
            <div class="card top-students overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="teacher-perfect.php">View All</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Students with Perfect Attendance </h5>
                  <table class="table table-hover" id="perfectAttendanceTable">
                    <thead>
                      <tr>
                        <th scope="col">Sr-Code</th>
                        <th scope="col">Student Name</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $perfect_attendance_result->fetch_assoc()) : ?>
                        <tr class="clickable-row" data-name="<?= htmlspecialchars($row['student_name']) ?>">
                          <td><?php echo $row['srcode']; ?> </td>
                          <td><?php echo $row['student_name']; ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div><!-- End Top Students with Most Late -->


            <div class="col-md-6 equal-height">
              <div class="card top-students overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li><a class="dropdown-item" href="teacher-absences.php">View All</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Students with Most Frequent Absences </h5>
                  <table class="table table-hover" id="absencesTable">
                    <thead>
                      <tr>
                        <th scope="col">Sr-Code</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Absences</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $absences_result->fetch_assoc()) : ?>
                        <tr class="clickable-row" data-name="<?= htmlspecialchars($row['student_name']) ?>">
                          <td><?php echo $row['srcode']; ?>
                          <td><?php echo $row['student_name']; ?></td>
                          <td class="fw-bold"><?php echo $row['absence_count']; ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Top Students with Most Absences -->

            <div class="col-md-12 equal-height">
            <div class="card top-students overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="teacher-tardiness.php">View All</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Students with Most Late Arrivals</h5>
                  <table class="table table-hover" id="lateTable">
                    <thead>
                      <tr>
                        <th scope="col">Sr-Code</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Late Count</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $late_result->fetch_assoc()) : ?>

                        <tr class="clickable-row" data-name="<?= htmlspecialchars($row['student_name']) ?>">
                          <td><?php echo $row['srcode']; ?> <!-- Display student code here --></td>
                          <td><?php echo $row['student_name']; ?></td>
                          <td class="fw-bold"><?php echo $row['late_count']; ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
         
            </div><!-- End Top Students with Most Late -->

          </div>
        </div><!-- End 5th Layer -->

  </main>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const maleCount = <?php echo $male_count; ?>;
      const femaleCount = <?php echo $female_count; ?>;

      const options = {
        series: [maleCount, femaleCount],
        chart: {
          type: 'pie',
          height: 500
        },
        labels: ['Male', 'Female'],
        colors: ['#4e73df', '#e74a3b'],
        legend: {
          position: 'top'
        },
        tooltip: {
          y: {
            formatter: function(value) {
              const total = maleCount + femaleCount;
              const percentage = ((value / total) * 100).toFixed(2);
              return `${value} (${percentage}%)`;
            }
          }
        },
        dataLabels: {
          enabled: true,
          formatter: function(value, {
            seriesIndex,
            w
          }) {
            const total = w.config.series.reduce((acc, val) => acc + val, 0);
            const count = w.config.series[seriesIndex];
            const percentage = ((count / total) * 100).toFixed(2);
            return `${count} (${percentage}%)`;
          },
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            colors: ['#fff']
          }
        },

      };

      const chart = new ApexCharts(document.querySelector("#genderChart"), options);
      chart.render();
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // PHP data passed to JavaScript
      const presentCount = <?php echo $present_count; ?>;
      const absentCount = <?php echo $absent_count; ?>;
      const lateCount = <?php echo $late_count; ?>;

      const total = presentCount + absentCount + lateCount;

      const options = {
        series: [presentCount, absentCount, lateCount],
        chart: {
          type: 'pie',
          height: 500
        },
        labels: ['Present', 'Absent', 'Late'],
        colors: ['#4caf50', '#f44336', '#ff9800'], // Colors for Present, Absent, and Late
        legend: {
          position: 'top'
        },
        tooltip: {
          y: {
            formatter: function(value) {
              const percentage = ((value / total) * 100).toFixed(2);
              return `${value} students (${percentage}%)`;
            }
          }
        },
        dataLabels: {
          enabled: true,
          formatter: function(value, {
            seriesIndex,
            w
          }) {
            const count = w.config.series[seriesIndex];
            const percentage = ((count / total) * 100).toFixed(2);
            return `${count} (${percentage}%)`; // Show count and percentage
          },
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            colors: ['#fff']

          }
        },

      };

      const chart = new ApexCharts(document.querySelector("#attendancePieChart"), options);
      chart.render();
    });
  </script>


  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const months = <?php echo json_encode($months); ?>;
      const presentCounts = <?php echo json_encode($presentCounts); ?>;

      const options = {
        series: [{
          name: 'Present Count',
          data: presentCounts
        }],
        chart: {
          type: 'line',
          height: 350
        },
        xaxis: {
          categories: months,
          title: {
            text: 'Month'
          }
        },
        yaxis: {
          title: {
            text: 'Number of Students Present'
          }
        },
        title: {
          text: 'Monthly Attendance Trend for Current Year',
          align: 'left'
        }
      };

      const chart = new ApexCharts(document.querySelector("#attendanceTrendChart"), options);
      chart.render();
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Daily Data
      const dailyDays = <?php echo json_encode($dailyDays); ?>;
      const dailyPresentCounts = <?php echo json_encode($dailyPresentCounts); ?>;

      // Daily Chart Options
      const options = {
        series: [{
          name: 'Present Count',
          data: dailyPresentCounts
        }],
        chart: {
          type: 'line',
          height: 350
        },
        xaxis: {
          categories: dailyDays,
          title: {
            text: 'Day of the Week'
          }
        },
        yaxis: {
          title: {
            text: 'Number of Students Present'
          }
        },
        title: {
          text: 'Daily Attendance Trend for Current Week',
          align: 'left'
        }
      };

      const chart = new ApexCharts(document.querySelector("#dailyTrendChart"), options);
      chart.render();
    });
  </script>

</body>

</html>