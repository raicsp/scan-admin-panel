<?php


include 'database/db_connect.php'; // Include the database connection file

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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Teacher Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_teacher'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = isset($_POST['password']) && !empty($_POST['password']) 
           ? password_hash($_POST['password'], PASSWORD_BCRYPT) 
           : password_hash($first_name . $last_name, PASSWORD_BCRYPT);

    $position = $_POST['position']; // Get the position value

    $sql = "INSERT INTO admin (firstname, lastname, email, password, position) VALUES ('$first_name', '$last_name', '$email', '$password', '$position')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Teacher added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['message_type'] = "danger";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}


// Handle Edit Teacher Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_teacher'])) {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $position = $_POST['position']; // Get the position value

    $sql = "UPDATE admin SET firstname='$first_name', lastname='$last_name', email='$email', position='$position' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Teacher updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['message_type'] = "danger";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle Delete Teacher
// Handle Delete Teacher
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_teacher'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM admin WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Teacher removed successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['message_type'] = "danger";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}


?>
