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

    // Check if email exists in the admin table
    $sql_check_admin = "SELECT id FROM admin WHERE email = ?";
    $stmt_check_admin = $conn->prepare($sql_check_admin);
    $stmt_check_admin->bind_param("s", $email);
    $stmt_check_admin->execute();
    $result_admin = $stmt_check_admin->get_result();

    // If email is not found in admin, check in users table
    if ($result_admin->num_rows == 0) {
        $sql_check_user = "SELECT id FROM users WHERE email = ?";
        $stmt_check_user = $conn->prepare($sql_check_user);
        $stmt_check_user->bind_param("s", $email);
        $stmt_check_user->execute();
        $result_user = $stmt_check_user->get_result();

        // If email is found in users, update the user
        if ($result_user->num_rows > 0) {
            $sql_update_user = "UPDATE users SET firstname = ?, lastname = ?, email = ?, profile_pic = ? WHERE email = ?";
            $stmt_update_user = $conn->prepare($sql_update_user);
            $stmt_update_user->bind_param("sssss", $firstname, $lastname, $email, $_SESSION['profile_pic'], $email);

            if ($stmt_update_user->execute()) {
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
        } else {
            // Email not found in users, show error
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Email Not Found!',
                    text: 'The email address is not found in both admin and users.',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    } else {
        // Update profile in admin table
        $sql_update_admin = "UPDATE admin SET firstname = ?, lastname = ?, email = ?, profile_pic = ? WHERE id = ?";
        $stmt_update_admin = $conn->prepare($sql_update_admin);
        $stmt_update_admin->bind_param("ssssi", $firstname, $lastname, $email, $_SESSION['profile_pic'], $admin_id);

        if ($stmt_update_admin->execute()) {
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
}
?>
