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

  <title>SCAN</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
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
      <h1>Account Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Account</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Teacher Accounts</h5>

                <!-- Add Teacher Button -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                  Add Administrator
                </button>
              </div>

              <!-- Search and Filter -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email">
                </div>
              </div>

              <!-- Table with Data -->
              <table id="accountsTable" class="table">
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
                                
                                  <td>{$row['firstname']} {$row['lastname']}</td>
                                  <td>{$row['email']}</td>
                                  <td>{$row['position']}</td>
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
    </section>

  </main><!-- End #main -->

  <!-- ======= Add Teacher Modal ======= -->
  <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTeacherModalLabel">Add Administrator</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Add Teacher Form -->
          <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="add_teacher" value="1">
            <div class="col-md-6">
              <label for="inputFirstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="inputFirstName" name="first_name" placeholder="John" required>
            </div>
            <div class="col-md-6">
              <label for="inputLastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="inputLastName" name="last_name" placeholder="Doe" required>
            </div>
            <div class="col-md-6">
              <label for="inputEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="inputEmail" name="email" placeholder="john.doe@example.com"
                required>
            </div>
            <div class="col-md-6">
                <label for="position">Position</label>
                <input type="text" class="form-control" id="position" name="position" placeholder="Chairperson" required>
            </div>


            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="gridCheck" required>
                <label class="form-check-label" for="gridCheck">
                  Confirm information is accurate
                </label>
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
            <div class="col-md-6">
              <label for="editFirstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="editFirstName" name="first_name" required>
            </div>
            <div class="col-md-6">
              <label for="editLastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="editLastName" name="last_name" required>
            </div>
            <div class="col-md-6">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required>
            </div>
            <div class="col-md-6">
              <label for="editPosition" class="form-label">Position</label>
              <input type="text" class="form-control" id="editPosition" name="position" required>
            </div>
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
<div class="modal fade" id="deleteTeacherModal" tabindex="-1" aria-labelledby="deleteTeacherModalLabel" aria-hidden="true">
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

// Delete teacher function
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

    // Search function
    document.getElementById('searchInput').addEventListener('input', function () {
      const searchQuery = this.value.toLowerCase();
      const rows = document.querySelectorAll('#accountsTable tbody tr');

      rows.forEach(row => {
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();

        if (name.includes(searchQuery) || email.includes(searchQuery)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  </script>

</body>

</html>
