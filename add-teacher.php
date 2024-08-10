<?php
$activePage = 'add-teacher';
include 'database/db_connect.php';
include 'database/db-add-teacher.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>SCAN</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    #alertContainer {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1050; /* Ensure it appears above other content */
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
      margin-left: auto; /* Align the button to the right */
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Teacher Information</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
          <li class="breadcrumb-item">Student</li>
          <li class="breadcrumb-item active">Add Teacher</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div id="alertContainer" class="container mt-3">
        <!-- Display success or error message -->
        <?php if (!empty($alertMessage)): ?>
          <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
            <?= $alertMessage ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Teacher Information Form</h5>
            
            </div>
            <div class="card-body">
              <!-- Multi Columns Form -->
              <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="col-md-6">
                  <label for="inputFirstName" class="form-label">First Name</label>
                  <input type="text" class="form-control <?php echo (!empty($firstNameErr)) ? 'is-invalid' : ''; ?>" id="inputFirstName" name="first_name" value="<?php echo $firstName; ?>" placeholder="John">
                  <span class="invalid-feedback"><?php echo $firstNameErr;?></span>
                </div>
                <div class="col-md-6">
                  <label for="inputLastName" class="form-label">Last Name</label>
                  <input type="text" class="form-control <?php echo (!empty($lastNameErr)) ? 'is-invalid' : ''; ?>" id="inputLastName" name="last_name" value="<?php echo $lastName; ?>" placeholder="Doe">
                  <span class="invalid-feedback"><?php echo $lastNameErr;?></span>
                </div>
                <div class="col-md-6">
                  <label for="inputEmail" class="form-label">Email</label>
                  <input type="email" class="form-control <?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>" id="inputEmail" name="email" value="<?php echo $email; ?>" placeholder="john.doe@example.com">
                  <span class="invalid-feedback"><?php echo $emailErr;?></span>
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
              </form><!-- End Multi Columns Form -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
