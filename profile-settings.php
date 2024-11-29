<?php

include 'database/db-header.php';
include 'database/db-profile-settings.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Administrator | Laboratory School | Batangas State University TNEU</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="bsu.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">



    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .profile-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .profile-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 90%;
            display: flex;
        }

        .profile-left {
            flex: 1;
            text-align: center;
            border-right: 1px solid #ddd;
            padding-right: 30px;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .upload-btn {
            display: inline-block;
            background-color: #f44336;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .save-pic-btn {
            display: none;
            margin-top: 10px;
        }

        .profile-right {
            flex: 2;
            padding-left: 30px;
        }

        .profile-right h3 {
            margin-bottom: 20px;
        }

        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include 'header.php'; ?>

    <div class="container-fluid">
        <div class="container profile-wrapper">
            <div class="profile-card">
                <div class="back-btn">
                    <?php
                    // Redirect based on user position
                    if (isset($_SESSION['position']) && $_SESSION['position'] === 'Teacher') {
                        echo '<a href="teacher-dashboard.php" class="btn btn-secondary"><i class="bi bi-house-door"></i> Home</a>';
                    } else {
                        echo '<a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-house-door"></i> Home</a>';
                    }
                    ?>
                </div>


                <!-- Left Side (Profile Picture Form) -->
                <div class="profile-left">
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_pic']) ?: 'adminimages/default-profile.png'; ?>"
                        alt="Profile Picture" class="profile-pic" id="profile-pic">
                    <h2><?php echo htmlspecialchars($_SESSION['firstname']) . ' ' . htmlspecialchars($_SESSION['lastname']); ?>
                    </h2>
                    <p><?php echo htmlspecialchars($_SESSION['position']); ?></p>
                    <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>

                    <label class="upload-btn" for="profile-pic-input">Select Profile Picture</label>
                    <input type="file" id="profile-pic-input" name="profile_pic" form="profile-settings-form"
                        style="display: none;" accept="image/*">

                    <button type="button" class="btn btn-primary save-pic-btn" id="save-pic-btn">Save Profile
                        Picture</button>
                </div>

                <!-- Right Side (Profile Edit Form with Tabs) -->
                <div class="profile-right">
                    <h3>Edit Profile</h3>
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="profile-info-tab" data-bs-toggle="tab" href="#profile-info"
                                role="tab" aria-controls="profile-info" aria-selected="true">Profile Info</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="change-password-tab" data-bs-toggle="tab" href="#change-password"
                                role="tab" aria-controls="change-password" aria-selected="false">Change Password</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="profileTabsContent">
                        <div class="tab-pane fade show active" id="profile-info" role="tabpanel"
                            aria-labelledby="profile-info-tab">
                            <form id="profile-settings-form" action="profile-settings.php" method="POST"
                                enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstname"
                                        value="<?php echo htmlspecialchars($_SESSION['firstname']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastname"
                                        value="<?php echo htmlspecialchars($_SESSION['lastname']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-primary" id="confirm-update-profile">Update
                                        Profile Info</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="change-password" role="tabpanel"
                            aria-labelledby="change-password-tab">
                            <form id="password-change-form" action="profile-settings.php" method="POST">
                                <div class="mb-3">
                                    <label for="previousPassword" class="form-label">Previous Password</label>
                                    <input type="password" class="form-control" id="previousPassword"
                                        name="previous_password" placeholder="Enter previous password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" name="new_password"
                                        placeholder="Enter new password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="retypePassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="retypePassword"
                                        name="retype_password" placeholder="Confirm new password" required>
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-primary" id="confirm-change-password">Change
                                        Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Profile Picture Upload
// Profile Picture Upload
document.getElementById('profile-pic-input').addEventListener('change', function() {
    const savePicBtn = document.getElementById('save-pic-btn');
    const profilePic = document.getElementById('profile-pic');

    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            profilePic.src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);

        savePicBtn.style.display = 'inline-block';
    }
});

document.getElementById('save-pic-btn').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to save this profile picture?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('profile-settings-form').submit();

            // Show success alert after submission
            Swal.fire({
                icon: 'success',
                title: 'Profile Picture Updated!',
                text: 'Your profile picture has been successfully updated.',
                confirmButtonText: 'OK'
            });
        }
    });
});

// Profile Info Update
document.getElementById('confirm-update-profile').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();

    if (!firstName || !lastName || !email) {
        Swal.fire({
            icon: 'error',
            title: 'Incomplete Information',
            text: 'All fields are required. Please fill them out before updating.',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to update your profile information?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('profile-settings-form').submit();

            // Show success alert after submission
            Swal.fire({
                icon: 'success',
                title: 'Profile Information Updated!',
                text: 'Your profile information has been successfully updated.',
                confirmButtonText: 'OK',

            });
        }
    });
});

// Password Change Update
document.getElementById('confirm-change-password').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const previousPassword = document.getElementById('previousPassword').value.trim();
    const newPassword = document.getElementById('newPassword').value.trim();
    const retypePassword = document.getElementById('retypePassword').value.trim();

    if (!previousPassword || !newPassword || !retypePassword) {
        Swal.fire({
            icon: 'error',
            title: 'Incomplete Information',
            text: 'All fields are required. Please fill them out before changing your password.',
            confirmButtonText: 'OK'
        });
        return;
    }

    if (newPassword !== retypePassword) {
        Swal.fire({
            icon: 'error',
            title: 'Passwords Do Not Match',
            text: 'Please make sure the new password and confirmation match.',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to change your password?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('password-change-form').submit();

            // Show success alert after submission
            Swal.fire({
                icon: 'success',
                title: 'Password Changed!',
                text: 'Your password has been successfully changed.',
                confirmButtonText: 'OK'
            });
        }
    });
});

    </script>
    
</body>

</html>