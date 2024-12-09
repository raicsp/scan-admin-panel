<?php
include 'database/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['firstname'], $_SESSION['lastname'], $_SESSION['email'], $_SESSION['position'])) {
    header('Location: login.php');
    exit;
}

// Get the admin ID from the session based on position
if ($_SESSION['position'] === 'Teacher') {
    // If position is "Teacher", use the 'id' session variable
    $admin_id = $_SESSION['id'];
} else {
    // If position is not "Teacher", use 'user_id'
    $admin_id = $_SESSION['user_id'];
}
// Fetch user data from session
$firstname = htmlspecialchars($_SESSION['firstname']);
$lastname = htmlspecialchars($_SESSION['lastname']);
$email = htmlspecialchars($_SESSION['email']);
$position = htmlspecialchars($_SESSION['position']);

// Update profile logic for name, email, and picture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstname'])) {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';

    // Check if a new profile picture is uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $profile_pic_tmp = $_FILES['profile_pic']['tmp_name'];
        $profile_pic_mime = mime_content_type($profile_pic_tmp);

        // Validate image MIME type
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profile_pic_mime, $allowed_mime_types)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type!',
                    text: 'Please upload a valid image (jpg, png, gif).',
                    confirmButtonText: 'OK'
                });
            </script>";
            exit;
        }

        // Resize the image
        list($width, $height) = getimagesize($profile_pic_tmp);
        $new_width = 200; // Set desired width
        $new_height = 200; // Set desired height
        $resized_image = imagecreatetruecolor($new_width, $new_height);

        // Create image resource based on MIME type
        switch ($profile_pic_mime) {
            case 'image/jpeg':
                $source_image = imagecreatefromjpeg($profile_pic_tmp);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($profile_pic_tmp);
                break;
            case 'image/gif':
                $source_image = imagecreatefromgif($profile_pic_tmp);
                break;
            default:
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type!',
                        text: 'Unsupported image format.',
                        confirmButtonText: 'OK'
                    });
                </script>";
                exit;
        }

        // Resize the source image into the new image
        imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Capture the resized image data into a blob
        ob_start();
        switch ($profile_pic_mime) {
            case 'image/jpeg':
                imagejpeg($resized_image);
                break;
            case 'image/png':
                imagepng($resized_image);
                break;
            case 'image/gif':
                imagegif($resized_image);
                break;
        }
        $profile_pic_data = ob_get_clean();

        // Clean up resources
        imagedestroy($source_image);
        imagedestroy($resized_image);
    } else {
        // Use the existing profile picture if no new one is uploaded
        $profile_pic_data = null;
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
            $stmt_update_user->bind_param("sssss", $firstname, $lastname, $email, $profile_pic_data, $email);

            if ($stmt_update_user->execute()) {
                if ($stmt_update_user->execute()) {
                    // Update session variables
                    $_SESSION['firstname'] = $firstname;
                    $_SESSION['lastname'] = $lastname;
                    $_SESSION['email'] = $email;
                
                    // If profile picture was updated
                    if ($profile_pic_data !== null) {
                        $_SESSION['profile_pic'] = 'data:image/' . explode('/', $profile_pic_mime)[1] . ';base64,' . base64_encode($profile_pic_data);
                    }
                
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Profile Updated!',
                            text: 'Your profile information has been updated successfully.',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
                
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
        $stmt_update_admin->bind_param("ssssi", $firstname, $lastname, $email, $profile_pic_data, $admin_id);

        if ($stmt_update_admin->execute()) {
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['email'] = $email;

            if ($profile_pic_data !== null) {
                $_SESSION['profile_pic'] = 'data:image/' . explode('/', $profile_pic_mime)[1] . ';base64,' . base64_encode($profile_pic_data);
            }

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


// Separate password update logic for admin and users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['previous_password'])) {
    $previous_password = $_POST['previous_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $retype_password = $_POST['retype_password'] ?? '';

    // Fetch current admin data for verification
    $sql_fetch_admin = "SELECT password FROM admin WHERE id = ?";
    $stmt_fetch_admin = $conn->prepare($sql_fetch_admin);
    $stmt_fetch_admin->bind_param("i", $admin_id);
    $stmt_fetch_admin->execute();
    $result_admin = $stmt_fetch_admin->get_result();

    // Check if admin data exists
    if ($result_admin->num_rows > 0) {
        $admin = $result_admin->fetch_assoc();

        // Verify previous password for admin
        if (!empty($previous_password) && password_verify($previous_password, $admin['password'])) {
            if (!empty($new_password) && $new_password === $retype_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in admin table
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
    } else {
        // If no admin data found, try user table
        $sql_fetch_user = "SELECT password FROM users WHERE email = ?";
        $stmt_fetch_user = $conn->prepare($sql_fetch_user);
        $stmt_fetch_user->bind_param("s", $email);
        $stmt_fetch_user->execute();
        $result_user = $stmt_fetch_user->get_result();

        // Check if user data exists
        if ($result_user->num_rows > 0) {
            $user = $result_user->fetch_assoc();

            // Verify previous password for user
            if (!empty($previous_password) && password_verify($previous_password, $user['password'])) {
                if (!empty($new_password) && $new_password === $retype_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password in users table
                    $sql_update_password_user = "UPDATE users SET password = ? WHERE email = ?";
                    $stmt_update_password_user = $conn->prepare($sql_update_password_user);
                    $stmt_update_password_user->bind_param("ss", $hashed_password, $email);
                    if ($stmt_update_password_user->execute()) {
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
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'No User Found!',
                    text: 'No user found with the provided details.',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    }
}
?>
