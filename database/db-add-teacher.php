<?php
session_start();
include 'database/db_connect.php'; // Include the database connection file
$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}
// Define variables and initialize with empty values
$firstName = $lastName = $email = "";
$firstNameErr = $lastNameErr = $emailErr = "";
$alertMessage = "";
$alertType = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (!isset($_POST["first_name"]) || empty(trim($_POST["first_name"]))) {
        $firstNameErr = "Please enter a first name.";
    } else {
        $firstName = trim($_POST["first_name"]);
    }

    // Validate last name
    if (!isset($_POST["last_name"]) || empty(trim($_POST["last_name"]))) {
        $lastNameErr = "Please enter a last name.";
    } else {
        $lastName = trim($_POST["last_name"]);
    }

    // Validate email
    if (!isset($_POST["email"]) || empty(trim($_POST["email"]))) {
        $emailErr = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Please enter a valid email.";
    } else {
        $email = trim($_POST["email"]);
    }
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Teacher Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_teacher'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Remove spaces and periods, and convert to lowercase
    $first_name_cleaned = strtolower(str_replace([' ', '.'], '', $first_name));
    $last_name_cleaned = strtolower(str_replace([' ', '.'], '', $last_name));

    // Combine first and last name for password
    $passwordPlain = $first_name_cleaned . $last_name_cleaned;
    
    // Hash the password
    $password = password_hash($passwordPlain, PASSWORD_BCRYPT);

    // Check if the email already exists
    $emailCheckSql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($emailCheckSql);

    if ($result->num_rows > 0) {
        // Email already exists
        $_SESSION['message'] = "Email is already registered.";
        $_SESSION['message_type'] = "error";
    } else {
        // Insert teacher data into the database
        $sql = "INSERT INTO users (firstname, lastname, email, password) VALUES ('$first_name', '$last_name', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Teacher added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
            $_SESSION['message_type'] = "error";
        }
    }

    // Redirect to the same page to display the message
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit Teacher Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_teacher'])) {
    $teacher_id = $_POST['teacher_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET firstname='$first_name', lastname='$last_name', email='$email' WHERE id='$teacher_id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Teacher updated successfully!";
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

// Handle Delete Teacher
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_teacher'])) {
    $teacher_id = $_POST['teacher_id'];

    $sql = "DELETE FROM users WHERE id='$teacher_id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Teacher removed successfully!";
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

// Close connection
$conn->close();
?>
