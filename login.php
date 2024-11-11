<?php
session_start(); // Start a new session
include 'database/db_connect.php';
include 'database/db-login.php';
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

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .custom-width {
            max-width: 75%;
            /* Adjust as needed */
            margin: 0 auto;
        }

        .form-label {
            display: block;
            /* Ensures labels take full width */
            margin-bottom: 0.5rem;
            /* Add some spacing below the label */
            max-width: 75%;
        }
    </style>
</head>

<body>

    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center ">


                                <img src="assets/img/header.png" alt="header" style="max-width: 100%; height: 100%; margin-bottom: 0;">



                            </div><!-- End Logo -->

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                        <p class="text-center small">Enter your email & password to login</p>
                                    </div>

                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $error_message; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form class="row g-3 needs-validation" method="POST" action="" novalidate>
                                        <div class="col-12 mb-3">
                                        
                                            <input type="email" name="email" class="form-control custom-width" id="yourEmail" required placeholder="Email">
                                            <div class="invalid-feedback text-center">Please enter your email.</div> <!-- Moved this div -->
                                        </div>

                                        <div class="col-12 mb-3">
                                            <input type="password" name="password" class="form-control custom-width" id="yourPassword" required placeholder="Password">
                                            <div class="invalid-feedback text-center">Please enter your password!</div> <!-- Moved this div -->
                                        </div>

                                        <div class="col-6 d-flex justify-content-center mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                                                <label class="form-check-label" for="showPassword">Show Password</label>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center mb-3">
                                            <button class="btn btn-success w-50 custom-width" type="submit">Sign in</button>
                                        </div>
                                    </form>
                                          <p class="text-center">
                                            <a href="forgotpassword.php">Forgot Password?</a>
                                        </p>


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

    <!-- JavaScript to toggle password visibility -->
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("yourPassword");
            var showPasswordCheckbox = document.getElementById("showPassword");
            if (showPasswordCheckbox.checked) {
                passwordInput.type = "text"; // Show password
            } else {
                passwordInput.type = "password"; // Hide password
            }
        }
    </script>

</body>

</html>