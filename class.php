<?php
$activePage = 'class'; // Set the active page
include 'database/db_connect.php'; // Include the database connection
include 'database/db-class.php';
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
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
  <!-- ======= Header ======= -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidebar.php'; ?>
  <!-- End Sidebar-->

  <!-- Alert Container -->



  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Class Allocation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Class Allocation</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">List of Classes</h5>

              <!-- Filter-->
              <div class="row mb-3">
                <div class="col-md-6">
                  <select id="gradeFilter" class="form-select">
                    <option value="">Select Grade</option>
                    <?php foreach ($grades as $grade) : ?>
                      <option value="<?php echo htmlspecialchars($grade); ?>"><?php echo htmlspecialchars($grade); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <select id="sectionFilter" class="form-select">
                    <option value="">Select Section</option>
                    <!-- Section options will be dynamically populated based on selected grade -->
                  </select>
                </div>
              </div>


              <!-- Table with stripped rows -->
              <table id="classTable" class="table">
                <thead>
                  <tr>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($classes as $class) : ?>
                    <tr>
                      <td><?php echo htmlspecialchars($class['grade_level']); ?></td>
                      <td><?php echo htmlspecialchars($class['section']); ?></td>
                      <td>
                        <select class="form-select" data-class-id="<?php echo htmlspecialchars($class['class_id']); ?>" onchange="updateTeacher(this)">
                          <option value="">Assign Teacher</option>
                          <?php foreach ($teachers as $teacher) : ?>
                            <option value="<?php echo htmlspecialchars($teacher['id']); ?>" <?php echo ($class['assigned_teacher_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($teacher['fullname']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>


    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmModalLabel">Confirm Assignment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p id="confirmMessage"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmButton">Confirm</button>
          </div>
        </div>
      </div>
    </div><!-- End Confirmation Modal -->
  </main><!-- End #main -->

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Filter script and updateTeacher function -->
  <script>
    $(document).ready(function() {
      // Initialize DataTable
      const dataTable = $('#classTable').DataTable({
        paging: true,
        searching: true,
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        language: {
          search: "Filter:",
          lengthMenu: "Show _MENU_ entries",
          info: "Showing _START_ to _END_ of _TOTAL_ entries",
          infoEmpty: "No entries available",
          emptyTable: "No data available",
        }
      });

      const gradeFilter = $('#gradeFilter');
      const sectionFilter = $('#sectionFilter');

      // Populate sections dynamically based on grade
      const sectionsByGrade = <?php echo json_encode($sectionsByGrade); ?>;

      gradeFilter.on('change', function() {
        const selectedGrade = $(this).val();

        // Clear current section options
        sectionFilter.html('<option value="">Select Section</option>');

        // Add options for the selected grade
        if (selectedGrade && sectionsByGrade[selectedGrade]) {
          sectionsByGrade[selectedGrade].forEach(section => {
            sectionFilter.append(new Option(section, section));
          });
        }

        // Apply grade filter
        filterTable();
      });

      // Apply filtering logic for grade and section
      const filterTable = () => {
        const gradeValue = gradeFilter.val();
        const sectionValue = sectionFilter.val();

        // Apply DataTables column filters
        dataTable.column(0).search(gradeValue ? `^${gradeValue}$` : '', true, false);
        dataTable.column(1).search(sectionValue ? `^${sectionValue}$` : '', true, false);
        dataTable.draw();
      };

      // Apply filtering when section changes
      sectionFilter.on('change', filterTable);
    });


    function updateTeacher(selectElement) {
      const classId = selectElement.getAttribute('data-class-id');
      const teacherId = selectElement.value;

      Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to assign this teacher to the class?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, assign it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // AJAX request
          $.post('database/db-class.php', {
            class_id: classId,
            teacher_id: teacherId
          }, function(response) {
            if (response.trim() === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Assigned!',
                text: 'The teacher has been successfully assigned.',
                timer: 1500,
                showConfirmButton: false
              }).then(() => location.reload());
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'There was an error assigning the teacher.',
              });
            }
          });
        }
      });
    }
  </script>

</body>

</html>
