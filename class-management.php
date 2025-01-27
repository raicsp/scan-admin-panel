<?php
$activePage = 'class-management';
include 'database/db_connect.php';
include 'database/db-class-management.php';
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
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

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

  <!-- Custom CSS for Alert and Button Layout -->
  <style>
    #alertContainer {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1050;
      /* Ensure it appears above other content */
    }

    .alert {
      margin: 0 auto;
      width: 90%;
      max-width: 600px;
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-header .btn-primary {
      margin-left: auto;
      /* Align the button to the right */
    }
  </style>
</head>

<body>

  <!-- Alert Container -->
  <?php if (!empty($alertMessage)): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
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
      <h1>Class Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Class Management</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Class Management</h5>
              <!-- Add Class Button -->

              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                Add Class
              </button>
            </div>

            <div class="card-body">
              <!-- Add Class Modal -->
              <!-- Add New Class Modal -->
              <div class="modal fade" id="addClassModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Add New Class</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form id="addClassForm" method="POST" action="">
                        <div class="mb-3">
                          <label for="inputGradeLevel" class="form-label">Grade Level</label>
                          <input type="text" class="form-control" id="inputGradeLevel" name="gradeLevel"
                            placeholder="e.g., Grade-1" required maxlength="20" pattern="[a-zA-Z0-9\s]+"
                            title="Only letters, numbers, and spaces are allowed, up to 50 characters">
                        </div>
                        <div class="mb-3">
                          <label for="inputSection" class="form-label">Section</label>
                          <input type="text" class="form-control" id="inputSection" name="section" value="N/A"
                            placeholder="e.g., A" required maxlength="20" pattern="[a-zA-Z0-9\s]+"
                            title="Only letters, numbers, and spaces are allowed, up to 10 characters">
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" onclick="submitClassForm()">Save changes</button>
                    </div>
                  </div>
                </div>
              </div><!-- End Add Class Modal -->

              <!-- Update Class Modal -->
              <div class="modal fade" id="updateClassModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Update Class</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form id="updateClassForm" method="POST" action="">
                        <input type="hidden" name="class_id" id="updateClassId">
                        <div class="mb-3">
                          <label for="updateGradeLevel" class="form-label">Grade Level</label>
                          <input type="text" class="form-control" id="updateGradeLevel" name="gradeLevel"
                            placeholder="e.g., 10th Grade" required maxlength="20" pattern="[a-zA-Z0-9\s]+"
                            title="Only letters, numbers, and spaces are allowed, up to 50 characters">
                        </div>
                        <div class="mb-3">
                          <label for="updateSection" class="form-label">Section</label>
                          <input type="text" class="form-control" id="updateSection" name="section"
                            placeholder="e.g., A" required maxlength="20" pattern="[a-zA-Z0-9\s]+"
                            title="Only letters, numbers, and spaces are allowed, up to 10 characters">
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" onclick="submitUpdateClassForm()">Save
                        changes</button>
                    </div>
                  </div>
                </div>
              </div><!-- End Update Class Modal -->


              <!-- Confirm Delete Modal -->
              <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Confirm Delete</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      Are you sure you want to delete this class?
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                    </div>
                  </div>
                </div>
              </div><!-- End Confirm Delete Modal -->

              <!-- Table with hoverable rows -->
              <div class="card-body">
                <!-- Table with hoverable rows -->
                <table class="table table-hover" id="classTable">
                  <thead>
                    <tr>

                      <th scope="col">Grade Level</th>
                      <th scope="col">Section</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($classes as $class): ?>
                      <tr data-class-id="<?= htmlspecialchars($class['class_id']) ?>">

                        <td><?= htmlspecialchars($class['grade_level']) ?></td>
                        <td><?= htmlspecialchars($class['section']) ?></td>
                        <td>
                          <button type="button" class="btn btn-warning btn-sm"
                            onclick="showUpdateClassModal('<?= $class['class_id'] ?>', '<?= htmlspecialchars($class['grade_level']) ?>', '<?= htmlspecialchars($class['section']) ?>'); event.stopPropagation();">Edit</button>
                          <button type="button" class="btn btn-danger btn-sm"
                            onclick="showConfirmDeleteModal('<?= $class['class_id'] ?>'); event.stopPropagation();">Delete</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <!-- End Table with hoverable rows -->
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

  <!-- Custom JS for Form Submission and Modal Handling -->
  <script>
    $(document).ready(function () {
      // Define custom sorting for grade levels
      jQuery.fn.dataTable.ext.type.order['grade-level-pre'] = function (data) {
        // Extract numeric part of the grade level using regex
        const match = data.match(/\d+/);
        return match ? parseInt(match[0], 10) : 0; // Return numeric value or 0 if no number found
      };

      $('#classTable').DataTable({
        searching: false, // Disable search functionality
        paging: true, // Enable pagination
        pageLength: 10, // Default number of rows per page
        lengthChange: false, // Disable the "entries per page" dropdown
        language: {
          zeroRecords: "No Classes Available",
          info: "Showing _START_ to _END_ of _TOTAL_ entries",
          infoEmpty: "No entries available",
        },
        columnDefs: [
          { type: 'grade-level', targets: 0 } // Apply custom sorting to the first column (Grade Level)
        ]
      });

      // Add click event for table rows
      $('#classTable tbody').on('click', 'tr', function () {
        var classId = $(this).data('class-id');
        if (classId) {
          window.location.href = 'class-students.php?class_id=' + classId;
        }
      });
    });



    document.querySelectorAll('#classTable tbody tr').forEach(row => {
      row.addEventListener('click', function () {
        window.location.href = 'class-students.php?class_id=' + this.dataset.classId;
      });
    });

    function submitClassForm() {
      document.getElementById('addClassForm').submit();
    }

    function showUpdateClassModal(classId, gradeLevel, section) {
      document.getElementById('updateClassId').value = classId;
      document.getElementById('updateGradeLevel').value = gradeLevel;
      document.getElementById('updateSection').value = section;
      var updateClassModal = new bootstrap.Modal(document.getElementById('updateClassModal'));
      updateClassModal.show();
    }

    function submitUpdateClassForm() {
      Swal.fire({
        title: 'Save Changes?',
        text: 'You are about to update this class.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('updateClassForm').submit();
        }
      });
    }


    function showConfirmDeleteModal(classId) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '?delete_id=' + classId;
        }
      });
    }

  </script>

  <?php if (!empty($alertMessage)): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: '<?= $alertType ?>',
          title: '<?= $alertType === "success" ? "Success!" : "Warning!" ?>',
          text: '<?= $alertMessage ?>',
          confirmButtonText: 'OK'
        });
      });
    </script>
  <?php endif; ?>


</body>

</html>