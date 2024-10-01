<?php

include 'database/db_connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, firstname, lastname, password, position, profile_pic FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $firstname, $lastname, $hashed_password, $position, $profile_pic);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables properly, including profile picture
            $_SESSION['user_id'] = $id;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['position'] = $position;
            $_SESSION['profile_pic'] = !empty($profile_pic) ? $profile_pic : 'assets/img/default-profile.png';
            $_SESSION['email'] = $email;  // Corrected this line
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "Email not found.";
    }

    $stmt->close();
}

$conn->close();
?>