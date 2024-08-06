<?php
include 'database/db_connect.php'; // Include the database connection
include 'database/db-student-details.php';
$studentName = $_GET['name'];
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
      <h1>Student Details: <span id="studentName"></span></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Student</li>
          <li class="breadcrumb-item active">Details</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Monthly Attendance Summary></span></h5>
              <!-- Monthly Attendance Summary Chart -->
              <div id="monthlyAttendanceChart1"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const urlParams = new URLSearchParams(window.location.search);
                  const studentName = urlParams.get('name');
                  document.getElementById('studentName').textContent = studentName;

                  const monthlyAttendanceData = <?php echo json_encode($monthlyAttendanceData); ?>;
                  const months = monthlyAttendanceData.map(item => item.month);
                  const daysPresent = monthlyAttendanceData.map(item => item.days_present);
                  const daysAbsent = monthlyAttendanceData.map(item => item.days_absent);

                  new ApexCharts(document.querySelector("#monthlyAttendanceChart1"), {
                    series: [{
                      name: 'Days Present',
                      data: daysPresent
                    }, {
                      name: 'Days Absent',
                      data: daysAbsent
                    }],
                    chart: {
                      type: 'line',
                      height: 350
                    },
                    stroke: {
                      curve: 'smooth'
                    },
                    dataLabels: {
                      enabled: false
                    },
                    xaxis: {
                      categories: months.map(month => new Date(0, month - 1).toLocaleString('default', {
                        month: 'short'
                      })),
                    }
                  }).render();
                });
              </script>
              <!-- End Monthly Attendance Summary Chart -->
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Monthly Attendance Summary ></span></h5>
              <!-- Monthly Attendance Summary Chart -->
              <div id="monthlyAttendanceChart2"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const urlParams = new URLSearchParams(window.location.search);
                  const studentName = urlParams.get('name');
                  document.getElementById('studentName').textContent = studentName;

                  const monthlyAttendanceData = <?php echo json_encode($monthlyAttendanceData); ?>;
                  const months = monthlyAttendanceData.map(item => item.month);
                  const daysPresent = monthlyAttendanceData.map(item => item.days_present);
                  const daysAbsent = monthlyAttendanceData.map(item => item.days_absent);

                  new ApexCharts(document.querySelector("#monthlyAttendanceChart2"), {
                    series: [{
                      name: 'Days Present',
                      data: daysPresent
                    }, {
                      name: 'Days Absent',
                      data: daysAbsent
                    }],
                    chart: {
                      type: 'bar',
                      height: 350
                    },
                    plotOptions: {
                      bar: {
                        borderRadius: 4,
                        horizontal: false,
                      }
                    },
                    dataLabels: {
                      enabled: false
                    },
                    xaxis: {
                      categories: months.map(month => new Date(0, month - 1).toLocaleString('default', {
                        month: 'short'
                      })),
                    }
                  }).render();
                });
              </script>
              <!-- End Monthly Attendance Summary Chart -->
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Attendance by Category for <span id="studentName"></span></h5>
              <!-- Attendance by Category Chart -->
              <div id="attendanceByCategoryChart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const urlParams = new URLSearchParams(window.location.search);
                  const studentName = urlParams.get('name');
                  document.getElementById('studentName').textContent = studentName;

                  const attendanceByCategoryData = <?php echo json_encode($attendanceByCategoryData); ?>;
                  const categories = attendanceByCategoryData.map(item => item.status);
                  const counts = attendanceByCategoryData.map(item => item.count);

                  new ApexCharts(document.querySelector("#attendanceByCategoryChart"), {
                    series: counts,
                    chart: {
                      height: 350,
                      type: 'pie',
                      toolbar: {
                        show: true
                      }
                    },
                    labels: categories.map(status => status.charAt(0).toUpperCase() + status.slice(1))
                  }).render();
                });
              </script>
              <!-- End Attendance by Category Chart -->
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