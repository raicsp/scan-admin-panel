<?php
// Start session to handle messages
session_start(); 

// Include database connection file
include 'database/db_connect.php'; 

// Define variables and initialize with empty values
$firstName = $lastName = $email = "";
$firstNameErr = $lastNameErr = $emailErr = "";
$alertMessage = "";
$alertType = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty(trim($_POST["first_name"]))) {
        $firstNameErr = "Please enter a first name.";
    } else {
        $firstName = trim($_POST["first_name"]);
    }

    // Validate last name
    if (empty(trim($_POST["last_name"]))) {
        $lastNameErr = "Please enter a last name.";
    } else {
        $lastName = trim($_POST["last_name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $emailErr = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Please enter a valid email.";
    } else {
        $email = trim($_POST["email"]);
    }
}

// Handle Add Admin Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_teacher'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Check if the email already exists
    $emailCheckQuery = "SELECT * FROM admin WHERE email = '$email'";
    $result = $conn->query($emailCheckQuery);

    if ($result->num_rows > 0) {
        // Email exists, show error message
        $_SESSION['message'] = "The email address is already in use. Please choose a different email.";
        $_SESSION['message_type'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Check if a password is provided, otherwise generate one from first and last names
    $passwordPlain = isset($_POST['password']) && !empty($_POST['password']) ? $_POST['password'] : $first_name . $last_name;

    // Convert the password to lowercase and remove spaces and periods
    $passwordPlain = strtolower($passwordPlain);  // Convert to lowercase
    $passwordPlain = str_replace([' ', '.'], '', $passwordPlain);  // Remove spaces and periods

    // Hash the password
    $password = password_hash($passwordPlain, PASSWORD_BCRYPT);

    $position = $_POST['position']; // Get the position value

    $sql = "INSERT INTO admin (firstname, lastname, email, password, position) VALUES ('$first_name', '$last_name', '$email', '$password', '$position')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Admin added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['message_type'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle Edit Admin Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_teacher'])) {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $position = $_POST['position']; // Get the position value

    $sql = "UPDATE admin SET firstname='$first_name', lastname='$last_name', email='$email', position='$position' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Admin updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['message_type'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle Delete Admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_teacher'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM admin WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Admin removed successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['message_type'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Display SweetAlert message
if (isset($_SESSION['message']) && isset($_SESSION['message_type'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '" . $_SESSION['message_type'] . "',
                title: '" . $_SESSION['message'] . "',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            });
        });
    </script>";

    // Clear session after message is displayed
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>