<?php
$activePage = 'account'; // Set the active page
include 'database/db_connect.php';
include 'database/db-add-teacher.php';
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
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Faculty Account Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Faculty Account Management</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Faculty Account</h5>

                <!-- Add Teacher Button -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                  Add Teacher
                </button>
              </div>

    

              <!-- Table with Data -->
              <table id="accountsTable" class="table">
                <thead>
                  <tr>
     
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody >
                  <?php
                  $result = $conn->query("SELECT * FROM users");
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                               
                                  <td>{$row['firstname']} {$row['lastname']}</td>
                                  <td>{$row['email']}</td>
                                  <td>
                                      <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editTeacherModal' onclick=\"editTeacher({$row['id']}, '{$row['firstname']}', '{$row['lastname']}', '{$row['email']}')\">Edit</button>
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
    </section>

  </main><!-- End #main -->

 <!-- ======= Add Teacher Modal ======= -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTeacherModalLabel">Add Teacher</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Add Teacher Form -->
        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <input type="hidden" name="add_teacher" value="1">
          <div class="col-md-6">
            <label for="inputFirstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="inputFirstName" name="first_name" placeholder="John" required 
              maxlength="50" pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed, up to 50 characters">
          </div>
          <div class="col-md-6">
            <label for="inputLastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="inputLastName" name="last_name" placeholder="Doe" required 
              maxlength="50" pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed, up to 50 characters">
          </div>
          <div class="col-md-6">
            <label for="inputEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="inputEmail" name="email" placeholder="john.doe@example.com" 
              required maxlength="50" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
              title="Please enter a valid email address, up to 50 characters">
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="gridCheck" required>
              <label class="form-check-label" for="gridCheck">Confirm information is accurate</label>
            </div>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
          </div>
        </form><!-- End Add Teacher Form -->
      </div>
    </div>
  </div>
</div><!-- End Add Teacher Modal -->
 
<!-- ======= Edit Teacher Modal ======= -->
<div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTeacherModalLabel">Edit Teacher</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Edit Teacher Form -->
        <form id="editTeacherForm" class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <input type="hidden" id="editTeacherId" name="teacher_id">
          <input type="hidden" name="edit_teacher" value="1">
          <div class="col-md-6">
            <label for="editFirstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="editFirstName" name="first_name" required 
              maxlength="50" pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed, up to 50 characters">
          </div>
          <div class="col-md-6">
            <label for="editLastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="editLastName" name="last_name" required 
              maxlength="50" pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed, up to 50 characters">
          </div>
          <div class="col-md-6">
            <label for="editEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editEmail" name="email" required 
              maxlength="50" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
              title="Please enter a valid email address, up to 50 characters">
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="editGridCheck" required>
              <label class="form-check-label" for="editGridCheck">Confirm information is accurate</label>
            </div>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
          </div>
        </form><!-- End Edit Teacher Form -->
      </div>
    </div>
  </div>
</div><!-- End Edit Teacher Modal -->

  <!-- Delete Teacher Form (hidden) -->
  <form id="deleteTeacherForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
    style="display:none;">
    <input type="hidden" name="delete_teacher" value="1">
    <input type="hidden" id="deleteTeacherId" name="teacher_id">
  </form>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show success or error messages using SweetAlert if present in the session
    <?php if (isset($_SESSION['message'])): ?>
        Swal.fire({
            title: 'Notification',
            text: "<?php echo $_SESSION['message']; ?>",
            icon: '<?php echo $_SESSION['message_type']; ?>',
            confirmButtonText: 'OK'
        });
        <?php
        // Clear the session message after displaying it
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    // Initialize a flag to track form submissions
    let isSubmitting = false;

    // Handle add teacher form submission and confirmation
    document.querySelector('form[action*="add_teacher"]').addEventListener('submit', function (event) {
      event.preventDefault(); // Prevent default form submission

      if (isSubmitting) return; // Prevent duplicate submissions

      Swal.fire({
        title: 'Are you sure?',
        text: "You are about to add a new teacher.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, add it!',
        cancelButtonText: 'No, cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          isSubmitting = true; // Set the flag to prevent further submissions
          this.submit(); // Submit the form
        } else {
          isSubmitting = false; // Reset the flag if canceled
        }
      });
    });

    // Handle edit teacher form submission and confirmation
    document.querySelector('#editTeacherForm').addEventListener('submit', function (event) {
      event.preventDefault(); // Prevent default form submission

      if (isSubmitting) return; // Prevent duplicate submissions

      Swal.fire({
        title: 'Are you sure?',
        text: "You are about to save changes for this teacher.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, save changes!',
        cancelButtonText: 'No, cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          isSubmitting = true; // Set the flag to prevent further submissions
          this.submit(); // Submit the form
        } else {
          isSubmitting = false; // Reset the flag if canceled
        }
      });
    });

    // Function to handle deleting a teacher with SweetAlert confirmation
    function deleteTeacher(id) {
      if (isSubmitting) return; // Prevent duplicate deletions
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
          isSubmitting = true; // Set the flag to prevent further submissions
          document.getElementById('deleteTeacherId').value = id;
          document.getElementById('deleteTeacherForm').submit();
        } else {
          isSubmitting = false; // Reset the flag if canceled
        }
      });
    }


    // Function to populate the edit modal with teacher data
    function editTeacher(id, firstName, lastName, email) {
      document.getElementById('editTeacherId').value = id;
      document.getElementById('editFirstName').value = firstName;
      document.getElementById('editLastName').value = lastName;
      document.getElementById('editEmail').value = email;
    }

    // Function to toggle password visibility
    function togglePassword() {
      const passwordField = document.getElementById('password');
      const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordField.setAttribute('type', type);
    }
  </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      const dataTable = new simpleDatatables.DataTable("#accountsTable", {
        searchable: true,
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
