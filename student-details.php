<?php
include 'database/db_connect.php'; // Include the database connection

// Get student srcode from query parameter
$studentSrCode = isset($_GET['srcode']) ? $_GET['srcode'] : null;

if ($studentSrCode === null) {
    echo "No student selected.";
    exit();
}

// Fetch student details based on the student srcode
$query = "SELECT studentID, srcode, name, gender, profile_pic, gmail, p_name, parent_contact, grade_level, section 
          FROM student 
          JOIN classes ON student.class_id = classes.class_id 
          WHERE srcode = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentSrCode);  // Use the srcode parameter
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $studentID = $row['studentID'];
  $studentName = $row['name'];
  $srcode = $row['srcode'];
  $gender = $row['gender'];
  $profilePic = $row['profile_pic']; // BLOB data
  $gmail = $row['gmail'];
  $p_name = $row['p_name'];
  $parentContact = $row['parent_contact'];
  $gradeLevel = $row['grade_level'];
  $section = $row['section'];

  // Convert BLOB to base64 string if a profile pic exists
  $profilePicBase64 = base64_encode($profilePic);
} else {
  echo "Student not found";
  exit();
}

$defaultProfilePic = 'assets/img/default.png';
$profilePicSrc = $profilePicBase64 ? "data:image/jpeg;base64,$profilePicBase64" : $defaultProfilePic;

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

// Fetching all attendance records for the student
$query = "SELECT date, status FROM attendance WHERE studentID = ? ORDER BY date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$attendanceResult = $stmt->get_result();

$attendanceRecords = [];
while ($row = $attendanceResult->fetch_assoc()) {
  $attendanceRecords[] = $row;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Laboratory School | Batangas State University TNEU</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
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
    .student-info-container {
      margin-bottom: 20px;
    }

    .profile-pic {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 50%;
      /* Make the image round */
      margin-right: auto;
      margin-left: auto;
      display: block;
      /* Center the image */
    }

    .row {
      display: flex;
      align-items: center;
      /* Align content vertically */
      justify-content: space-between;
      /* Separate the student info and the image */
    }

    .card {
      margin-bottom: 20px;
    }

    /* Ensure charts are of equal height */
    .equal-height {
      height: 350px;
      /* Fixed height to ensure charts are equal in size */
    }

    @media (max-width: 768px) {
      .text-md-end {
        text-align: center;
        /* Center the image on smaller screens */
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
      <h1>Student Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
          <li class="breadcrumb-item active">Student Information</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <!-- Student Information Container -->
      <div class="student-info-container">
        <div class="card">
          <div class="card-body text-center" style="padding:50px;">
            <!-- Profile Picture at the top, centered -->
            <img src="<?= htmlspecialchars($profilePicSrc) ?>" alt="Profile Picture" class="rounded-circle profile-pic">
            <!-- Student Name below the profile picture -->
            <h1><?= htmlspecialchars($studentName) ?></h1>
            <h5><?= htmlspecialchars($srcode) ?></h5>

            <!-- Student Details -->
            <div class="row" style="margin-top: 20px;">
              <!-- First row: Grade-Section and Parent Name -->
              <div class="col-md-6 text-start">

                <label>Grade-Section:</label>
                <input type="text" class="form-control"
                  value="<?= htmlspecialchars($gradeLevel) ?> <?= htmlspecialchars($section) ?>" readonly>
              </div>
              <div class="col-md-6 text-start">
                <label>Parent Name:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($p_name) ?>" readonly>
              </div>

            </div>

            <div class="row" style="margin-top: 20px;">
              <!-- Second row: Parent Email and Parent Contact -->
              <div class="col-md-6 text-start">
                <label>Parent Contact Number:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($parentContact) ?>" readonly>
              </div>
              <div class="col-md-6 text-start">
                <label>Parent Email:</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($gmail) ?>" readonly>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- End Student Information Container -->

      <!-- Charts Container -->
      <div class="row">
        <div class="col-lg-6 equal-height">
          <div class="card" style="height: 435px;">
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
          <div class="card" style="height: 435px;">
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

      <!-- HTML for Attendance Table and Filter -->
      <div class="card" style="margin-top: 100px; height: 500px;">
        <div class="card-body">
          <h5 class="card-title">Attendance Records</h5>

          <div class="row mb-3">
            <!-- Status Filter -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="statusFilter">Filter by Status:</label>
                <select id="statusFilter" class="form-control" onchange="filterAttendance()">
                  <option value="All">All</option>
                  <option value="Present">Present</option>
                  <option value="Absent">Absent</option>
                  <option value="Late">Late</option>
                </select>
              </div>
            </div>

            <!-- Date Filter -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="dateFilter">Filter by Date:</label>
                <input type="date" id="dateFilter" class="form-control" onchange="filterAttendance()">
              </div>
            </div>

            <!-- Month Filter -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="monthFilter">Filter by Month:</label>
                <input type="month" id="monthFilter" class="form-control" onchange="filterAttendance()">
              </div>
            </div>
          </div>

          <!-- Display Record Count -->
          <p>Showing <span id="visibleCount">0</span> records</p>


          <!-- Scrollable Table Container -->
          <div style="max-height: 300px; overflow-y: auto;">
            <table class="table table-striped" id="attendanceTable">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($attendanceRecords as $record): ?>
                  <tr data-date="<?= htmlspecialchars($record['date']) ?>"
                    data-status="<?= htmlspecialchars($record['status']) ?>">
                    <td><?= htmlspecialchars($record['date']) ?></td>
                    <td><?= htmlspecialchars($record['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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

  <script>
    // Add event listeners to clear conflicting filters
    document.getElementById("dateFilter").addEventListener("change", function () {
      document.getElementById("monthFilter").value = ""; // Clear month filter if a date is selected
      filterAttendance(); // Call the filter function
    });

    document.getElementById("monthFilter").addEventListener("change", function () {
      document.getElementById("dateFilter").value = ""; // Clear date filter if a month is selected
      filterAttendance(); // Call the filter function
    });

    document.getElementById("statusFilter").addEventListener("change", filterAttendance);

    // Main filter function
    function filterAttendance() {
      var statusFilter = document.getElementById("statusFilter").value;
      var dateFilter = document.getElementById("dateFilter").value;
      var monthFilter = document.getElementById("monthFilter").value;
      var rows = document.querySelectorAll("#attendanceTable tbody tr");
      var visibleCount = 0; // Initialize the count
      

      rows.forEach(function (row) {
        var status = row.getAttribute("data-status");
        var date = row.getAttribute("data-date");
        var rowMonth = date ? date.slice(0, 7) : "";

        // Define match conditions for each filter
        var statusMatch = (statusFilter === "All" || status === statusFilter);
        var dateMatch = (dateFilter !== "" && date === dateFilter);
        var monthMatch = (monthFilter !== "" && rowMonth === monthFilter);

        // Display the row if it matches all active filters
        if (statusMatch && (dateFilter ? dateMatch : monthFilter ? monthMatch : true)) {
          row.style.display = ""; // Show the row
          visibleCount++; // Increment the count of visible rows
        } else {
          row.style.display = "none"; // Hide the row
        }
      });

      // Update the visible count display
      document.getElementById("visibleCount").textContent = visibleCount;
    }

  </script>


</body>

</html>
