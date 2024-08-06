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

  <title>SCAN</title>
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
  <div id="alertContainer" class="container mt-3">
    <?php if (!empty($alertMessage)) : ?>
      <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
        <?= $alertMessage ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
  </div>


  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Class Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Class</li>
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
                    <?php foreach ($sections as $section) : ?>
                      <option value="<?php echo htmlspecialchars($section); ?>"><?php echo htmlspecialchars($section); ?></option>
                    <?php endforeach; ?>
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
    document.addEventListener('DOMContentLoaded', function() {
      const dataTable = new simpleDatatables.DataTable("#classTable", {
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

    document.addEventListener('DOMContentLoaded', (event) => {
      const gradeFilter = document.getElementById('gradeFilter');
      const sectionFilter = document.getElementById('sectionFilter');
      const table = document.getElementById('classTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      const filterTable = () => {
        const gradeValue = gradeFilter.value;
        const sectionValue = sectionFilter.value;

        for (let row of rows) {
          const grade = row.cells[0].textContent;
          const section = row.cells[1].textContent;

          if ((gradeValue === "" || grade === gradeValue) && (sectionValue === "" || section === sectionValue)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      };

      gradeFilter.addEventListener('change', filterTable);
      sectionFilter.addEventListener('change', filterTable);
    });

    function updateTeacher(selectElement) {
      const classId = selectElement.getAttribute('data-class-id');
      const teacherId = selectElement.value;

      // Prepare confirmation message
      const selectedTeacher = selectElement.options[selectElement.selectedIndex].text;
      document.getElementById('confirmMessage').textContent = `Are you sure you want to assign ${selectedTeacher} to this class?`;

      // Show confirmation modal
      var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
      myModal.show();

      // Handle confirm button click
      document.getElementById('confirmButton').onclick = function() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'database/db-class.php', true); // Adjust path to match the location of db-class.php
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            console.log('Request completed with status:', xhr.status);
            if (xhr.status === 200) {
              console.log('Response:', xhr.responseText);
              if (xhr.responseText.includes('successfully')) {
                location.href = 'class.php?status=success'; // Redirect to show alert
              } else {
                console.error('Error in response:', xhr.responseText);
                location.href = 'class.php?status=error'; // Redirect to show error alert
              }
            } else {
              console.error('Error updating teacher:', xhr.status, xhr.statusText);
              location.href = 'class.php?status=error'; // Redirect to show error alert
            }
          }
        };
        xhr.send('class_id=' + encodeURIComponent(classId) + '&teacher_id=' + encodeURIComponent(teacherId));
        myModal.hide();
      };
    }
  </script>
</body>

</html>