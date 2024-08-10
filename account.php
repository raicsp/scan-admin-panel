<?php
$activePage = 'account'; // Set the active page
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
              <h5 class="card-title">Teacher Accounts</h5>

              <!-- Search and Filter -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email">
                </div>
                <div class="col-md-6">
                  <select id="statusFilter" class="form-select">
                    <option value="">Select Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Declined">Declined</option>
                  </select>
                </div>
              </div>

              <!-- Table with stripped rows -->
              <table id="accountsTable" class="table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>John Smith</td>
                    <td>john.smith@example.com</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                    <td>
                      <button class="btn btn-success btn-sm" onclick="approveAccount()">Approve</button>
                      <button class="btn btn-danger btn-sm" onclick="declineAccount()">Decline</button>
                    </td>
                  </tr>
                  <tr>
                    <td>Jane Doe</td>
                    <td>jane.doe@example.com</td>
                    <td><span class="badge bg-success">Approved</span></td>
                    <td>
                      <button class="btn btn-success btn-sm" disabled>Approve</button>
                      <button class="btn btn-danger btn-sm" onclick="declineAccount()">Decline</button>
                    </td>
                  </tr>
                  <tr>
                    <td>Jane Doe</td>
                    <td>jane.doe@example.com</td>
                    <td><span class="badge bg-danger">Declined</span></td>
                    <td>
                      <button class="btn btn-success btn-sm" onclick="approveAccount()">Approve</button>
                      <button class="btn btn-danger btn-sm" disabled>Decline</button>
                    </td>
                  </tr>


                  <!-- Add more rows as needed -->
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <!-- Include footer content here -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

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

  <!-- Filter script -->
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const searchInput = document.getElementById('searchInput');
      const statusFilter = document.getElementById('statusFilter');
      const table = document.getElementById('accountsTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      const filterTable = () => {
        const searchValue = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        for (let row of rows) {
          const name = row.cells[0].textContent.toLowerCase();
          const email = row.cells[1].textContent.toLowerCase();
          const status = row.cells[2].textContent.trim();

          if (
            (name.includes(searchValue) || email.includes(searchValue)) &&
            (statusValue === "" || status === statusValue)
          ) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      };

      searchInput.addEventListener('input', filterTable);
      statusFilter.addEventListener('change', filterTable);

      const dataTable = new simpleDatatables.DataTable("#accountsTable", {
        searchable: false,
        paging: true,
        fixedHeight: true
      });
    });

    function approveAccount() {
      alert('Account approved.');
      // Implement approval logic here
    }

    function declineAccount() {
      alert('Account declined.');
      // Implement decline logic here
    }
  </script>

</body>

</html>