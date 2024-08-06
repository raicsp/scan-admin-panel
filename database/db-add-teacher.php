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

    // Check input errors before inserting in database
    if (empty($firstNameErr) && empty($lastNameErr) && empty($emailErr)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (firstname, lastname, email) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_firstName, $param_lastName, $param_email);

            // Set parameters
            $param_firstName = $firstName;
            $param_lastName = $lastName;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $alertMessage = "Teacher added successfully!";
                $alertType = "success";
            } else {
                $alertMessage = "Something went wrong. Please try again later.";
                $alertType = "danger";
            }

            // Close statement
            $stmt->close();
        }
    } else {
        $alertMessage = "Please correct the errors and try again.";
        $alertType = "danger";
    }

    // Close connection
    $conn->close();
}
?>