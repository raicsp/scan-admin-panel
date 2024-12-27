<?php
$activePage = 'class-promotion';
include 'database/db_connect.php';
session_start(); // Start the session for flash messages
$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}
$alertMessage = '';
$alertType = '';

// Display alert messages if set in session
if (isset($_SESSION['alertMessage'])) {
  $alertMessage = $_SESSION['alertMessage'];
  $alertType = $_SESSION['alertType'];
  unset($_SESSION['alertMessage']);
  unset($_SESSION['alertType']);
}
$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
  // Display error message with image
  echo '<div style="text-align: center;">';
  echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
  echo '<p><strong>ACCESS DENIED</strong></p>';
  echo '</div>';
  exit; // Terminate the script after displaying the error
}



// Fetch the logged-in user's position
$userPosition = trim($_SESSION['position'] ?? '');

// Define grade condition based on the user's position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
  // Allow access only to Kinder to Grade-6
  $gradeCondition = "AND grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
  // Allow access only to Grade-7 to Grade-12
  $gradeCondition = "AND grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

// Fetch classes based on the grade condition
$classes = [];
$query = "SELECT class_id, grade_level, section FROM classes $gradeCondition WHERE class_id IN (1,2, 3, 4, 5, 6, 7)";
$result = $conn->query($query);


if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
  }
} else {
  echo 'No classes found for your assigned grade levels.';
}

// Handle form submission for promoting students
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['promote_students'])) {
    // Delete students with class_id 1 and 7 first
    $deleteQuery = "DELETE FROM student WHERE class_id IN (1, 7)";

    // Execute the delete query
    if ($conn->query($deleteQuery) === TRUE) {
      // After deleting, promote students
      $promotionQuery = "UPDATE student SET class_id = CASE
              WHEN class_id = 2 THEN 3
              WHEN class_id = 3 THEN 4
              WHEN class_id = 4 THEN 5
              WHEN class_id = 5 THEN 6
              WHEN class_id = 6 THEN 7
            
              ELSE class_id END";

      // Execute the promotion query
      if ($conn->query($promotionQuery) === TRUE) {
        $_SESSION['alertMessage'] = 'Students Have Been Promoted Successfully';
        $_SESSION['alertType'] = 'success';
      } else {
        $_SESSION['alertMessage'] = 'An error occurred while promoting students.';
        $_SESSION['alertType'] = 'error';
      }
    } else {
      $_SESSION['alertMessage'] = 'An error occurred while deleting students.';
      $_SESSION['alertType'] = 'error';
    }

    // Redirect back to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Administrator | Laboratory School | Batangas State University TNEU</title>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- Alert Container -->
  <?php if (!empty($alertMessage)): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: '<?= $alertType === "success" ? "success" : "error" ?>', // Dynamically set the icon
          title: '<?= $alertType === "success" ? "Success!" : "Error!" ?>',
          text: '<?= $alertMessage ?>',
          confirmButtonText: 'OK'
        });
      });
    </script>
  <?php endif; ?>

  <!-- ======= Header ======= -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidebar.php'; ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Class Promotion</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Class Promotion</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <h5 class="card-title ms-3">Elementary</h5>

            <div class="card-header d-flex justify-content-between align-items-center">

              <!-- Left: Promote Button -->
              <div>
                <button type="button" class="btn btn-primary" id="promoteButton">
                  Promote
                </button>
              </div>

              <!-- Right: Combo Box -->
              <div>
                <select class="form-select" style="width: auto;" id="categoryDropdown" onchange="handleSelectionChange(this)">
                  <option value="" selected disabled>Select Category</option>
                  <option value="promote.php">Elementary</option>
                  <option value="promote-hs.php">High School</option>
                  <option value="promote-shs.php">Senior High School</option>
                </select>
              </div>
            </div>


            <div class="card-body">
              <table class="table table-hover" id="classTable">
                <thead>
                  <tr>
                    <th scope="col">Grade Level</th>
                    <th scope="col">Section</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($classes as $class): ?>
                    <tr data-class-id="<?= htmlspecialchars($class['class_id']) ?>">
                      <td><?= htmlspecialchars($class['grade_level']) ?></td>
                      <td><?= htmlspecialchars($class['section']) ?></td>
                    </tr>
                  <?php endforeach; ?>
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
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Custom JS for Form Submission and SweetAlert -->
  <script>
    $(document).ready(function() {
      $('#promoteButton').click(function() {
        Swal.fire({
          title: 'Are you sure?',
          text: 'You are about to promote the students and delete students with class_id 1 and 7.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
        }).then((result) => {
          if (result.isConfirmed) {
            // Submit the form after confirmation
            $.ajax({
              type: 'POST',
              url: '',
              data: {
                promote_students: true
              },
              success: function() {
                Swal.fire(
                  'Success!',
                  'Students have been promoted.',
                  'success'
                );
              },
              error: function() {
                Swal.fire(
                  'Error!',
                  'An error occurred while promoting students.',
                  'error'
                );
              }
            });
          }
        });
      });
    });

    function handleSelectionChange(select) {
      const selectedValue = select.value;
      if (selectedValue) {
        window.location.href = selectedValue;
      }
    }
  </script>

</body>

</html>