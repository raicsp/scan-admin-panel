<?php
// Database configuration
$host = 'localhost'; // Change if needed
$db_name = 'scan'; // Change to your database name
$db_username = 'your_db_username'; // Change to your database username
$db_password = 'your_db_password'; // Change to your database password

// Initialize variables to hold error messages and success status
$error_message = '';
$success_message = false;

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve and sanitize user inputs
        $firstname = filter_var(trim($_POST['firstname']), FILTER_SANITIZE_STRING);
        $lastname = filter_var(trim($_POST['lastname']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        // Check for empty fields
        if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
            $error_message = "Please fill in all fields.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL statement
            $stmt = $pdo->prepare("INSERT INTO admin (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)");
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            // Execute the query
            if ($stmt->execute()) {
                $success_message = true; // Set success message to true
            } else {
                $error_message = "Error: Could not register user. Please try again.";
            }
        }
    }
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>SCAN - Register</title>
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

  <!-- SweetAlert CSS and JS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">SCAN</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">
                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create Your Account</h5>
                    <p class="text-center small">Enter your details to register</p>
                  </div>

                  <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                      <?php echo $error_message; ?>
                    </div>
                  <?php endif; ?>

                  <form class="row g-3 needs-validation" novalidate method="POST">

                    <div class="col-12">
                      <label for="yourFirstName" class="form-label">First Name</label>
                      <input type="text" name="firstname" class="form-control" id="yourFirstName" required>
                      <div class="invalid-feedback">Please enter your first name.</div>
                    </div>

                    <div class="col-12">
                      <label for="yourLastName" class="form-label">Last Name</label>
                      <input type="text" name="lastname" class="form-control" id="yourLastName" required>
                      <div class="invalid-feedback">Please enter your last name.</div>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="yourEmail" required>
                      <div class="invalid-feedback">Please enter your email.</div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <input type="checkbox" id="showPassword" onclick="togglePassword()">
                      <label for="showPassword">Show Password</label>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Register</button>
                    </div>

                  </form>

                  <?php if ($success_message): ?>
                    <script>
                      swal("Success!", "Registration successful!", "success");
                    </script>
                  <?php endif; ?>

                </div>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

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

  <script>
    function togglePassword() {
      var passwordField = document.getElementById("yourPassword");
      if (passwordField.type === "password") {
        passwordField.type = "text";
      } else {
        passwordField.type = "password";
      }
    }
  </script>

</body>

</html>
