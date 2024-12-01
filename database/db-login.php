<?php

include 'database/db_connect.php';

// Check if the form is submitted for login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user_found = false;

        // First check in the `admin` table
        $stmt = $conn->prepare("SELECT id, firstname, lastname, password, position, profile_pic FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $firstname, $lastname, $hashed_password, $position, $profile_pic);
            $stmt->fetch();
            $user_found = true;

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                $_SESSION['position'] = $position;
                $_SESSION['email'] = $email;

                // Convert BLOB to Base64 and set default if no image is uploaded
                if (!empty($profile_pic)) {
                    $profile_pic_base64 = 'data:image/jpeg;base64,' . base64_encode($profile_pic);
                } else {
                    $profile_pic_base64 = 'assets/img/default-profile.png'; // Path to default image
                }
                $_SESSION['profile_pic'] = $profile_pic_base64;

                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Incorrect password.";
            }
        }
        $stmt->close();

        // If email is not found in `admin`, check in the `users` table
        if (!$user_found) {
            $stmt = $conn->prepare("SELECT id, firstname, lastname, password, class_id, profile_pic FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $firstname, $lastname, $hashed_password, $class_id, $profile_pic);
                $stmt->fetch();
                $user_found = true;

                if (password_verify($password, $hashed_password)) {
                    $_SESSION['id'] = $id;
                    $_SESSION['firstname'] = $firstname;
                    $_SESSION['lastname'] = $lastname;
                    $_SESSION['class_id'] = $class_id;
                    $_SESSION['email'] = $email;
                    $_SESSION['position'] = "Teacher";

                    // Convert BLOB to Base64 and set default if no image is uploaded
                    if (!empty($profile_pic)) {
                        $profile_pic_base64 = 'data:image/jpeg;base64,' . base64_encode($profile_pic);
                    } else {
                        $profile_pic_base64 = 'assets/img/default-profile.png'; // Path to default image
                    }
                    $_SESSION['profile_pic'] = $profile_pic_base64;

                    header("Location: teacher-dashboard.php");
                    exit();
                } else {
                    $error_message = "Incorrect password.";
                }
            }
            $stmt->close();
        }

        if (!$user_found) {
            $error_message = "Email not found.";
        }
    }

    // Check if the form is for sending the verification code (no password field)
    if (isset($_POST['send_code'])) {
        $email = $_POST['email'];
        $verificationCode = rand(100000, 999999);
        $_SESSION['verification_code'] = $verificationCode;
        $isSent = sendVerificationCode($email, $verificationCode);

        if ($isSent) {
            echo "<script>alert('Verification code sent!');</script>";
        } else {
            echo "<script>alert('Failed to send verification code. Please try again.');</script>";
        }
    }

    // Handle code validation after the user enters the code
    if (isset($_POST['validate_code'])) {
        $inputCode = $_POST['code'];
        if ($inputCode == $_SESSION['verification_code']) {
            echo "<script>alert('Code matched!');</script>";
        } else {
            echo "<script>alert('Invalid verification code!');</script>";
        }
    }
}

$conn->close();
?>
