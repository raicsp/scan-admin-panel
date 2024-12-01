<?php
$activePage = 'administrator'; // Set the active page
include 'database/db_connect.php';
include 'database/db-administrator.php';
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
        .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
  </style>

</head>

<body>

  <!-- ======= Header ======= -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidebar.php'; ?>
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Administrators Account Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Administrators Account</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Administrators Account</h5>

                <!-- Add Admin Button -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                  Add Administrator
                </button>
              </div>



              <!-- Table with Data -->
              <div class="table-responsive">
              <table id="accountsTable" class="table table-striped display">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $result = $conn->query("SELECT * FROM admin");
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                <td>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['position']) . "</td>
                <td>
                  <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editTeacherModal' onclick=\"editTeacher({$row['id']}, '{$row['firstname']}', '{$row['lastname']}', '{$row['email']}', '{$row['position']}')\">Edit</button>
                  <button class='btn btn-danger btn-sm' onclick='deleteTeacher({$row['id']})'>Delete</button>
                </td>
              </tr>";
                    }
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Add Admin Modal ======= -->
  <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTeacherModalLabel">Add Administrator</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Add Admin Form -->
          <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="add_teacher" value="1">
            <div class="col-md-6">
              <label for="inputFirstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="inputFirstName" name="first_name" placeholder="John" required
                maxlength="50" pattern="[A-Za-z\s.]{1,50}"
                title="First Name should contain only letters and spaces, max 50 characters.">
            </div>

            <!-- Last Name -->
            <div class="col-md-6">
              <label for="inputLastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="inputLastName" name="last_name" placeholder="Doe" required
                maxlength="50" pattern="[A-Za-z\s]{1,50}"
                title="Last Name should contain only letters and spaces, max 50 characters.">
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label for="inputEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="inputEmail" name="email" placeholder="john.doe@example.com"
                required maxlength="50" title="Please enter a valid email address, max 50 characters.">
            </div>

            <!-- Position -->
            <div class="col-md-6">
              <label for="position" class="form-label">Position</label>
              <input type="text" class="form-control" id="position" name="position" placeholder="Chairperson" required
                maxlength="50" pattern="[A-Za-z\s]{1,50}"
                title="Position should only contain letters and spaces, max 50 characters.">
            </div>

            <!-- Confirmation Check -->
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="gridCheck" required>
                <label class="form-check-label" for="gridCheck">
                  Confirm information is accurate
                </label>
              </div>
            </div>

            <!-- Submit and Reset Buttons -->
            <div class="text-center">
              <button type="submit" class="btn btn-primary"
                onclick="confirmSubmit(event, 'add_teacher')">Submit</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
          </form><!-- End Add Teacher Form -->
        </div>
      </div>
    </div>
  </div><!-- End Add Teacher Modal-->

  <!-- ======= Edit Teacher Modal ======= -->
  <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTeacherModalLabel">Edit Teacher</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Edit Teacher Form -->
          <form id="editTeacherForm" class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
            method="post">
            <input type="hidden" id="editTeacherId" name="id">
            <input type="hidden" name="edit_teacher" value="1">
            <!-- First Name -->
            <div class="col-md-6">
              <label for="editFirstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="editFirstName" name="first_name" required maxlength="50"
              pattern="[A-Za-z\s.]{1,50}"
                title="First Name should contain only letters and spaces, max 50 characters.">
            </div>

            <!-- Last Name -->
            <div class="col-md-6">
              <label for="editLastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="editLastName" name="last_name" required maxlength="50"
                pattern="[A-Za-z\s]{1,50}" title="Last Name should contain only letters and spaces, max 50 characters.">
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required maxlength="50"
                title="Please enter a valid email address, max 50 characters.">
            </div>

            <!-- Position -->
            <div class="col-md-6">
              <label for="editPosition" class="form-label">Position</label>
              <input type="text" class="form-control" id="editPosition" name="position" required maxlength="50"
                pattern="[A-Za-z\s]{1,50}" title="Position should only contain letters and spaces, max 50 characters.">
            </div>

            <!-- Submit and Reset Buttons -->
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
          </form><!-- End Edit Teacher Form -->
          <!-- Hidden Delete Form -->
          <form id="deleteTeacherForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="hidden" name="delete_teacher" value="1">
            <input type="hidden" id="deleteTeacherId" name="id">
          </form>

        </div>
      </div>
    </div>
  </div><!-- End Edit Teacher Modal-->


  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteTeacherModal" tabindex="-1" aria-labelledby="deleteTeacherModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteTeacherModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this teacher?
          <input type="hidden" id="deleteTeacherId" name="delete_teacher_id"> <!-- Hidden ID -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
      </div>
    </div>
  </div>



  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

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
    // Edit teacher function
    function editTeacher(id, firstName, lastName, email, position) {
      document.getElementById('editTeacherId').value = id;
      document.getElementById('editFirstName').value = firstName;
      document.getElementById('editLastName').value = lastName;
      document.getElementById('editEmail').value = email;
      document.getElementById('editPosition').value = position;
    }

    // Delete teacher function
    function deleteTeacher(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('deleteTeacherId').value = id; // Set the id in hidden input
          document.getElementById('deleteTeacherForm').submit(); // Submit the form
        }
      });
    }

    // Confirmation for form submission
    function confirmSubmit(event, actionType) {
      event.preventDefault(); // Prevent the default form submission
      Swal.fire({
        title: 'Are you sure?',
        text: "Please confirm your action.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          event.target.form.submit(); // Submit the form after confirmation
        }
      });
    }



    $(document).ready(function () {
      // Initialize DataTable with options
      $('#accountsTable').DataTable({
        paging: true, // Enable pagination
        searching: true, // Enable search functionality
        pageLength: 10, // Number of rows per page
        language: {
          search: "Search:", // Customize search input placeholder
          lengthMenu: "Show _MENU_ entries per page",
          zeroRecords: "No results found",
          info: "Showing _START_ to _END_ of _TOTAL_ results",
          infoEmpty: "No results available",
          infoFiltered: "(filtered from _MAX_ total entries)"
        }
      });
    });


    <?php if (isset($_SESSION['message'])): ?>
      Swal.fire({
        icon: '<?php echo $_SESSION['message_type']; ?>',
        title: '<?php echo $_SESSION['message']; ?>',
        showConfirmButton: true, // Show OK button
        confirmButtonText: 'OK', // Text for the OK butto
        timer: 1500
      }).then(() => {
        // Optionally redirect after showing the SweetAlert message
        window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';
      });
    <?php endif; ?>

  </script>

  <?php
  // Clear session message after displaying it
  unset($_SESSION['message']);
  unset($_SESSION['message_type']);
  ?>
</body>

</html>