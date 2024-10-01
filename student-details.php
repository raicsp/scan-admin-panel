<?php
include 'database/db_connect.php'; // Include the database connection

// Get student name from query parameter
$studentName = $_GET['name'];

// Fetch student details based on the student name
$query = "SELECT studentID, name, gender, profile_pic, gmail, p_name, parent_contact, grade_level, section 
          FROM student 
          JOIN classes ON student.class_id = classes.class_id 
          WHERE name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentName);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $studentID = $row['studentID'];
  $studentName = $row['name'];
  $gender = $row['gender'];
  $profilePic = $row['profile_pic']; // BLOB data
  $gmail = $row['gmail'];
  $p_name = $row['p_name'];
  $parentContact = $row['parent_contact'];
  $gradeLevel = $row['grade_level'];
  $section = $row['section'];

  // Convert BLOB to base64 string
  $profilePicBase64 = base64_encode($profilePic);

} else {
  echo "Student not found";
  exit();
}

// Fetching monthly attendance summary data for the student (same as before)
$monthlyAttendanceData = [];
$attendanceByCategoryData = [];

// Monthly Attendance Summary
$query = "SELECT MONTH(date) as month, 
                 SUM(status='Present') as days_present, 
                 SUM(status='Absent') as days_absent 
          FROM attendance 
          WHERE studentID = ? 
          GROUP BY MONTH(date)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $monthlyAttendanceData[] = $row;
}

// Attendance by Category
$query = "SELECT status, COUNT(*) as count 
          FROM attendance 
          WHERE studentID = ? 
          GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $attendanceByCategoryData[] = $row;
}

$stmt->close();
$conn->close();
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
  <style>
    .student-info-container {
      margin-bottom: 20px;
    }

    .profile-pic {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 50%; /* Make the image round */
      margin-right: auto;
      margin-left: auto;
      display: block; /* Center the image */
    }

    .row {
      display: flex;
      align-items: center; /* Align content vertically */
      justify-content: space-between; /* Separate the student info and the image */
    }

    .card {
      margin-bottom: 20px;
    }

    /* Ensure charts are of equal height */
    .equal-height {
      height: 350px; /* Fixed height to ensure charts are equal in size */
    }

    @media (max-width: 768px) {
      .text-md-end {
        text-align: center; /* Center the image on smaller screens */
        margin-top: 15px;
      }

      .profile-pic {
        margin-top: 15px;
      }
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
      <h1>Student Details</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Student</li>
          <li class="breadcrumb-item active">Details</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <!-- Student Information Container -->
      <div class="student-info-container">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title"></h5>
      <!-- Student Information -->
      <div class="row">
        <div class="col-md-8">
          <!-- Student Details -->
          <h1><?= htmlspecialchars($studentName) ?><br></h1>
          <p><strong>Grade-Section:</strong> <?= htmlspecialchars($gradeLevel) ?> <?= htmlspecialchars($section) ?><br></p>
          <p><strong>Parent Email Address: </strong><?= htmlspecialchars($gmail) ?><br></p>
          <p><strong>Parent Name: </strong><?= htmlspecialchars($p_name) ?><br></p>
          <p><strong>Parent Contact Number:</strong> <?= htmlspecialchars($parentContact) ?><br></p>
        </div>
        <div class="col-md-4 text-md-end">
          <!-- Profile Picture -->
          <img src="data:image/jpeg;base64,<?= $profilePicBase64 ?>" alt="Profile Picture" class="rounded-circle profile-pic">
        </div>
      </div>
    </div>
  </div>
</div>

      <!-- End Student Information Container -->

      <!-- Charts Container -->
      <div class="row">
        <div class="col-lg-6 equal-height">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Monthly Attendance Trends</h5>
              <!-- Monthly Attendance Summary Chart -->
              <div id="monthlyAttendanceChart1"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
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
                      height: 270
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

        <div class="col-lg-6  equal-height">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Attendance Distribution</h5>
              <!-- Attendance by Category Chart -->
              <div id="attendanceByCategoryChart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
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
      <!-- End Charts Container -->

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
