<?php
session_start();
include 'database/db_connect.php';  // Include the database connection
include 'sendVerificationCode.php'; // Include the function for sending verification code email

// Initialize variables
$verificationCode = '';
$error_message = '';
$success_message = '';
$match_message = '';  // Variable to store the match confirmation message

// Handle form submission to send verification code
if (isset($_POST['send_code'])) {
    $email = $_POST['email'];

    // Store the email in session for later use
    $_SESSION['email'] = $email;

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, generate verification code and send email
        $verificationCode = rand(100000, 999999);
        $_SESSION['verification_code'] = $verificationCode;

        // Call the function to send the verification code email
        $isSent = sendVerificationCode($email, $verificationCode);

        if ($isSent) {
            $success_message = "Verification code sent to $email. Please check your inbox.";
        } else {
            $error_message = "Failed to send verification code. Please try again.";
        }
    } else {
        // Email does not exist
        $error_message = "No account found with that email address. Please try again.";
    }

    $stmt->close();
}

// Handle code validation after the user enters the code
if (isset($_POST['validate_code'])) {
    $inputCode = $_POST['code'];
    if ($inputCode == $_SESSION['verification_code']) {
        $_SESSION['verified'] = true; // Mark as verified
        $match_message = "The verification code matches! You can now reset your password.";  // Show match confirmation
        // Do not redirect, stay on the same page for the password reset
    } else {
        $error_message = "Invalid verification code!";
    }
}

// Handle password reset
if (isset($_POST['change_password'])) {
    if (isset($_SESSION['verified']) && $_SESSION['verified']) {
        $newPassword = $_POST['new_password'];
        $email = $_SESSION['email'];  // The email entered during verification

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            $success_message = "Your password has been successfully changed!";
            unset($_SESSION['verification_code']); // Clear verification code session
            unset($_SESSION['verified']); // Clear the verified flag
        } else {
            $error_message = "Failed to update password. Please try again.";
        }

        $stmt->close();
    } else {
        $error_message = "Please verify the code before resetting your password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Forgot Password</title>
    <link href="assets/img/bsu.png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">Forgot Password</h5>
                    <p class="text-center mb-4">Enter your email to receive a verification code</p>

                    <!-- Display Error Message -->
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Display Success Message -->
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display Match Confirmation -->
                    <?php if ($match_message): ?>
                        <div class="alert alert-info" role="alert">
                            <?php echo $match_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Step 1: Request Verification Code -->
                    <?php if (!isset($_SESSION['verified'])): ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" required placeholder="Email" autofocus>
                            </div>
                            <button type="submit" name="send_code" class="btn btn-primary w-100">Send Verification Code</button>
                        </form>
                    <?php endif; ?>

                    <!-- Step 2: Enter Verification Code -->
                    <?php if (isset($_SESSION['verification_code']) && !isset($_SESSION['verified'])): ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <input type="text" name="code" class="form-control" required placeholder="Enter Verification Code">
                            </div>
                            <button type="submit" name="validate_code" class="btn btn-primary w-100">Validate Code</button>
                        </form>
                    <?php endif; ?>

                    <!-- Step 3: Change Password -->
                    <?php if (isset($_SESSION['verified'])): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>" /> 
                            <div class="mb-3">
                                <input type="password" name="new_password" class="form-control" required placeholder="New Password">
                            </div>
                            <button type="submit" name="change_password" class="btn btn-success w-100">Change Password</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>

</body>
</html>
