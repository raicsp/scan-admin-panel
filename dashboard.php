<?php
$activePage = 'dashboard';
include 'database/db_connect.php'; // Include the database connection
include 'database/db-dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>SCAN - Dashboard</title>
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

  <!-- Chart Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

            <div class="col-md-4">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Total Students</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="student"><?php echo $student; ?></h6>
                      <span class="text-muted small pt-2 ps-1">student</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Students Card -->

            <div class="col-md-4">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Present Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="present-today"><?php echo $present_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Present Today Card -->

            <div class="col-md-4">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Absent Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-x-circle"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="absent-today"><?php echo $absent_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Absent Today Card -->

          </div>
        </div><!-- End 1st Layer -->

        <!-- 2nd Layer: 2 Cards -->
        <div class="col-12">
          <div class="row">

            <div class="col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Total Late Students</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-clock"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="late-today"><?php echo $late_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Late Students Card -->

            <div class="col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Total Teachers</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="teacher"><?php echo $teacher; ?></h6>
                      <span class="text-muted small pt-2 ps-1">teacher</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Teachers Card -->

          </div>
        </div><!-- End 2nd Layer -->

        <!-- 3rd Layer: Attendance Overview and Attendance by Grade -->
        <div class="col-12">
          <div class="row">

            <!-- Attendance Overview Line Chart -->
            <div class="col-md-6 equal-height">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Attendance Overview <span>/This Month</span></h5>
                  <div id="attendance-line-chart"></div>
                </div>
              </div>
            </div><!-- End Attendance Overview Line Chart -->
            <!-- Attendance Distribution Pie Chart -->
            <div class="col-md-6 equal-height">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Attendance Summary Chart</h5>
                  <canvas id="attendanceSummaryChart"></canvas>
                </div>
              </div>
            </div><!-- End Attendance Summary Chart -->
          </div><!-- End Attendance Distribution Pie Chart -->

        </div>
      </div><!-- End 3rd Layer -->

      <!-- 4th Layer: Attendance Distribution and Monthly Attendance -->
      <div class="col-12">
        <div class="row">



          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Attendance By Grade</h5>
                <canvas id="attendanceByGradeChart"></canvas>
              </div>
            </div>
          </div><!-- End Attendance By Grade -->
          <!-- Monthly Attendance Stacked Bar Chart -->
          <div class="col-md-6 equal-height">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Monthly Attendance</h5>
                <canvas id="attendance-stacked-bar-chart"></canvas>
              </div>
            </div>
          </div><!-- End Monthly Attendance Stacked Bar Chart -->

        </div>
      </div><!-- End 4th Layer -->

      <!-- 5th Layer: Top Students with Most Absences and Top Students with Most Late -->
      <div class="col-12">
        <div class="row">

          <!-- Top Students with Most Absences -->
          <div class="col-md-6 equal-height">
            <div class="card top-students overflow-auto">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>
                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>
              <div class="card-body pb-0">
                <h5 class="card-title">Students with Most Absences <span>| Today</span></h5>
                <table class="table table-hover" id="absencesTable">
                  <thead>
                    <tr>
                      <th scope="col">Student Name</th>
                      <th scope="col">Grade</th>
                      <th scope="col">Absences</th>
                      <th scope="col">Percentage</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $absences_result->fetch_assoc()) : ?>
                      <tr class="clickable-row" data-name="<?= htmlspecialchars($row['student_name']) ?>">
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?php echo $row['grade_level']; ?></td>
                        <td class="fw-bold"><?php echo $row['absence_count']; ?></td>
                        <td><?php echo $row['percentage']; ?>%</td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div><!-- End Top Students with Most Absences -->

          <!-- Top Students with Most Late -->
          <div class="col-md-6 equal-height">
            <div class="card top-students overflow-auto">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>
                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>
              <div class="card-body pb-0">
                <h5 class="card-title"> Students with Most Late Arrivals <span>| Today</span></h5>
                <table class="table table-hover" id="studentsTable">
                  <thead>
                    <tr>
                      <th scope="col">Student Name</th>
                      <th scope="col">Grade</th>
                      <th scope="col">Late</th>
                      <th scope="col">Percentage</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $late_result->fetch_assoc()) : ?>
                      <tr class="clickable-row" data-name="<?= htmlspecialchars($row['student_name']) ?>">
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?php echo $row['grade_level']; ?></td>
                        <td class="fw-bold"><?php echo $row['late_count']; ?></td>
                        <td><?php echo $row['percentage']; ?>%</td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div><!-- End Top Students with Most Late -->

        </div>
      </div><!-- End 5th Layer -->

      </div>
    </section>

  </main><!-- End #main -->

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

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Chart Data from PHP -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var ctx = document.getElementById('attendanceSummaryChart').getContext('2d');
      var attendanceSummaryChart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ['Present', 'Late', 'Absent'],
          datasets: [{
            data: [
              <?php echo $present_today; ?>,
              <?php echo $late_today; ?>,
              <?php echo $absent_today; ?>
            ],
            backgroundColor: [
              'rgba(75, 192, 192, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
              'rgba(75, 192, 192, 1)',
              'rgba(255, 159, 64, 1)',
              'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
        }
      });
    });

    document.addEventListener("DOMContentLoaded", function() {
      var ctx = document.getElementById('attendanceByGradeChart').getContext('2d');
      var attendanceByGradeChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($attendance_by_grade['labels']); ?>,
          datasets: <?php echo json_encode($attendance_by_grade['datasets']); ?>
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });

    document.addEventListener("DOMContentLoaded", function() {
      var ctx = document.getElementById('attendance-stacked-bar-chart').getContext('2d');
      var stackedBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($stacked_bar_data['labels']); ?>,
          datasets: <?php echo json_encode($stacked_bar_data['datasets']); ?>
        },
        options: {
          responsive: true,
          scales: {
            x: {
              stacked: true
            },
            y: {
              stacked: true
            }
          }
        }
      });
    });
    document.addEventListener("DOMContentLoaded", function() {
      // Line Chart
      var lineChartOptions = {
        series: [{
          name: 'Present',
          data: <?php echo json_encode($attendance_overview['presentCounts']); ?>
        }, {
          name: 'Absent',
          data: <?php echo json_encode($attendance_overview['absentCounts']); ?>
        }],
        chart: {
          height: 350,
          type: 'line'
        },
        stroke: {
          width: [2, 2],
          curve: 'smooth'
        },
        xaxis: {
          categories: <?php echo json_encode($attendance_overview['days']); ?>
        },
        yaxis: {
          title: {
            text: 'Number of Students'
          }
        },
        colors: ['#FF1654', '#247BA0']
      };

      var attendanceLineChart = new ApexCharts(document.querySelector("#attendance-line-chart"), lineChartOptions);
      attendanceLineChart.render();
    });
    document.addEventListener('DOMContentLoaded', (event) => {
      const table = document.getElementById('studentsTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        row.classList.add('clickable-row');
        row.addEventListener('click', function() {
          const studentName = row.getAttribute('data-name');
          window.location.href = `student-details.php?name=${encodeURIComponent(studentName)}`;
        });
      }
    });
    document.addEventListener('DOMContentLoaded', (event) => {
      const table = document.getElementById('absencesTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        row.classList.add('clickable-row');
        row.addEventListener('click', function() {
          const studentName = row.getAttribute('data-name');
          window.location.href = `student-details.php?name=${encodeURIComponent(studentName)}`;
        });
      }
    });
  </script>

</body>

</html>