<?php
session_start();
include 'database/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['firstname'], $_SESSION['lastname'], $_SESSION['email'], $_SESSION['position'], $_SESSION['profile_pic'])) {
    header('Location: login.php');
    exit;
}

// Get the admin ID from the session
$admin_id = $_SESSION['user_id'];

// Define the upload directory for profile pictures
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/scan-admin/adminimages/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
}
 
// Fetch user data from session
$firstname = htmlspecialchars($_SESSION['firstname']);
$lastname = htmlspecialchars($_SESSION['lastname']);
$email = htmlspecialchars($_SESSION['email']);
$position = htmlspecialchars($_SESSION['position']);
$profile_pic = htmlspecialchars($_SESSION['profile_pic'] ?? 'adminimages/default-profile.png');

// Update profile logic for name, email, and picture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstname'])) {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';

    // Check if a new profile picture is uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $profile_pic = basename($_FILES['profile_pic']['name']);
        $profile_pic_tmp = $_FILES['profile_pic']['tmp_name'];
        
        // Check for valid image file types (optional)
        $allowed_file_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = pathinfo($profile_pic, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($file_extension), $allowed_file_types)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type!',
                    text: 'Please upload a valid image (jpg, jpeg, png, gif).',
                    confirmButtonText: 'OK'
                });
            </script>";
            exit;
        }

        // Move the uploaded file to the adminimages directory
        if (move_uploaded_file($profile_pic_tmp, $upload_dir . $profile_pic)) {
            $_SESSION['profile_pic'] = 'adminimages/' . $profile_pic; // Update session with new path
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed!',
                    text: 'There was an error uploading the image. Please try again.',
                    confirmButtonText: 'OK'
                });
            </script>";
            exit;
        }
    }

    // Update user information (excluding password)
    $sql_update = "UPDATE admin SET firstname = ?, lastname = ?, email = ?, profile_pic = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $firstname, $lastname, $email, $_SESSION['profile_pic'], $admin_id);

    if ($stmt_update->execute()) {
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['email'] = $email;

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: 'Your profile information has been updated successfully.',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Update Failed!',
                text: 'Failed to update profile. Please try again.',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}

// Separate password update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['previous_password'])) {
    $previous_password = $_POST['previous_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $retype_password = $_POST['retype_password'] ?? '';

    // Fetch current admin data for verification
    $sql_fetch = "SELECT password FROM admin WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $admin_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $admin = $result->fetch_assoc();

    // Verify previous password
    if (!empty($previous_password) && password_verify($previous_password, $admin['password'])) {
        if (!empty($new_password) && $new_password === $retype_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $sql_update_password = "UPDATE admin SET password = ? WHERE id = ?";
            $stmt_update_password = $conn->prepare($sql_update_password);
            $stmt_update_password->bind_param("si", $hashed_password, $admin_id);
            if ($stmt_update_password->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Updated!',
                        text: 'Your password has been updated successfully.',
                        confirmButtonText: 'OK'
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed!',
                        text: 'Failed to update password. Please try again.',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch!',
                    text: 'The new passwords do not match. Please try again.',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Incorrect Password!',
                text: 'The previous password is incorrect. Please try again.',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>