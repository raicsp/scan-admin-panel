<?php
include 'database/db_connect.php';
include 'database/db-manage-years.php';

$activePage = 'manage-years';

$archivedData = getArchivedYears($conn);

// Check if the Archive button was clicked
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive_data'])) {
  // Call the archive function
  $archiveResult = archiveStudentData($conn);
  if ($archiveResult === "success") {
    $message = "Data successfully archived!";
  } else {
    $message = $archiveResult; // Display any error messages
  }
}
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
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- jQuery Library -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- SweetAlert Library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

  <!-- Header -->
  <?php include 'header.php'; ?>
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>SCAN</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Manage Academic Years</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">MANAGE ACADEMIC YEARS <br> <span> <?= date("F j, Y") ?></span> </h5>

              <!-- Archive Data Section -->
              <h6><b>Archive Current Year Data</b></h6>


              <!-- Form to submit archive action -->
              <form method="POST" action="">
                <!-- <button type="submit" name="archive_data" class="btn btn-warning mb-3">Archive Record</button> -->
                <button class="btn btn-warning mb-3">Archive Record</button>
              </form>

              <!-- <p class="text-muted">This action will transfer current student and attendance data to previous tables.</p> -->

              <!-- View Archived Data Section -->
              <h6><b>View Archived Data</b></h6>
              <table class="table table-bordered " id="archiveTable">
                <thead>
                  <tr>
                    <th>Academic Year</th>
                    <th>Date Archived</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($archivedData as $data) : ?>
                    <tr>
                      <td><?= htmlspecialchars($data['academic_year']) ?></td>
                      <td><?= htmlspecialchars($data['date_archived']) ?></td>
                      <td>
                        <!-- View and Delete Buttons Side by Side -->
                        <form method="POST" action="" style="display: inline-block;">
                          <input type="hidden" name="academic_year" value="<?= htmlspecialchars($data['academic_year']) ?>">

                          <!-- View Button -->
                          <a href="view-archive.php?academic_year=<?= urlencode($data['academic_year']) ?>" class="btn btn-primary">View</a>


                          <!-- Delete Button -->
                          <button type="submit" name="delete_academic_year" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this academic year?');">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>

              </table>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <!-- Back to Top Button -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- SweetAlert Script -->
  <script>
    $(document).ready(function() {
      // Archive Button Click Handler
      $('#archiveButton').on('click', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Show SweetAlert confirmation dialog
        Swal.fire({
          title: 'Are you sure?',
          text: 'Do you really want to archive the current year data? This action cannot be undone.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, archive it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            // If confirmed, submit the form
            $('#archiveForm').submit();
          }
        });
      });

      // Delete Button Click Handler
      $('.deleteButton').on('click', function(e) {
        e.preventDefault(); // Prevent default form submission

        const academicYear = $(this).data('year'); // Get the academic year from data attribute

        // Show SweetAlert confirmation dialog for deletion
        Swal.fire({
          title: 'Are you sure?',
          text: `Do you really want to delete the archived data for ${academicYear}? This action cannot be undone.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            // If confirmed, set the academic year in the form and submit it
            $('#deleteForm input[name="academic_year"]').val(academicYear);
            $('#deleteForm').submit();
          }
        });
      });

      // Display archive or delete message from the server
      <?php if (!empty($message)) : ?>
        Swal.fire({
          title: 'Message',
          text: '<?= htmlspecialchars($message) ?>',
          icon: '<?= strpos($message, "successfully") !== false ? "success" : "error" ?>',
          confirmButtonText: 'OK'
        });
      <?php endif; ?>
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dataTable = new simpleDatatables.DataTable("#archiveTable", {
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