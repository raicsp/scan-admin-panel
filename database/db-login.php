<?php

include 'database/db_connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, firstname, lastname, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $firstname, $lastname, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $id; // Store user ID in session
            $_SESSION['firstname'] = $firstname; // Store first name in session
            $_SESSION['lastname'] = $lastname;   // Store last name in session
            header("Location: dashboard.php"); // Redirect to dashboard or another page
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
