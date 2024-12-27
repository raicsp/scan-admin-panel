<?php
include 'database/db_connect.php';
$activePage = 'class-promotion';
session_start(); // Start the session for flash messages
$userPosition = trim($_SESSION['position'] ?? '');

if ($userPosition === '') {
    // Display error message with image
    echo '<div style="text-align: center;">';
    echo '<img src="./adminimages/denied.png" alt="Error" style="width: 500px; height: auto;"/>';
    echo '<p><strong>ACCESS DENIED</strong></p>';
    echo '</div>';
    exit; // Terminate the script after displaying the error
}
// Fetch classes with next grade sections
$classes = [];
$query = "SELECT c.class_id, c.grade_level, c.section, 
          (SELECT GROUP_CONCAT(section SEPARATOR ',') 
           FROM classes 
           WHERE grade_level = CONCAT('Grade-', CAST(SUBSTRING(c.grade_level, 7) + 1 AS UNSIGNED))) AS next_sections
          FROM classes c
          WHERE class_id IN (8, 9, 10, 11, 12, 13)";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['next_sections'] = explode(',', $row['next_sections']);
        $classes[] = $row;
    }
}

// Handle form submission for promotion
// Handle form submission for promotion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['promote_students'])) {
    $selectedSections = $_POST['selected_sections']; // Array of selected sections indexed by class_id

    // Check if all sections are selected
    foreach ($classes as $class) {
        $classId = $class['class_id'];
        if (empty($selectedSections[$classId])) {
            $_SESSION['alertMessage'] = 'Please select a section for all rows.';
            $_SESSION['alertType'] = 'error';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    $usedSections = [];
    foreach ($selectedSections as $classId => $section) {
        if (in_array($section, $usedSections)) {
            $_SESSION['alertMessage'] = 'Duplicate sections selected. Please choose unique sections.';
            $_SESSION['alertType'] = 'error';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        $usedSections[] = $section;
    }

    // Delete Grade 10 classes and their students first
    $deleteStudentsQuery = "DELETE FROM student WHERE class_id IN (14, 15)";


    if (!$conn->query($deleteStudentsQuery) || !$conn->query($deleteClassesQuery)) {
        $_SESSION['alertMessage'] = 'An error occurred while deleting Grade 10 data.';
        $_SESSION['alertType'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Proceed with updating students for promotion
    // Proceed with updating students for promotion
    foreach ($selectedSections as $classId => $section) {
        $promotionQuery = "UPDATE student SET class_id = (
        SELECT class_id FROM classes WHERE grade_level = CONCAT('Grade-', CAST(SUBSTRING(grade_level, 7) + 1 AS UNSIGNED)) 
        AND section = '$section'
    ) WHERE class_id = $classId";

        // Log the query for debugging
        error_log("Promotion Query: " . $promotionQuery);

        if (!$conn->query($promotionQuery)) {
            error_log("Error: " . $conn->error); // Log SQL error
            $_SESSION['alertMessage'] = 'An error occurred while promoting students.';
            $_SESSION['alertType'] = 'error';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    $_SESSION['alertMessage'] = 'Students Have Been Promoted Successfully';
    $_SESSION['alertType'] = 'success';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Administrator | Laboratory School | Batangas State University TNEU</title>
    <link href="assets/img/bsu.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Class Promotion</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Class Promotion</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <h5 class="card-title ms-3">High School</h5>
                        <div class="card-header d-flex justify-content-between align-items-center">

                            <!-- Left: Promote Button -->
                            <div>
                                <button type="button" class="btn btn-primary" id="promoteButton">
                                    Promote
                                </button>
                            </div>

                            <!-- Right: Combo Box -->
                            <div>
                                <select class="form-select" style="width: auto;" id="categoryDropdown" onchange="handleSelectionChange(this)">
                                    <option value="" selected disabled>Select Category</option>
                                    <option value="promote.php">Elementary</option>
                                    <option value="promote-hs.php">High School</option>
                                    <option value="promote-shs.php">Senior High School</option>
                                </select>
                            </div>
                        </div>


                        <div class="card-body">
                            <form id="promotionForm" method="POST">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Grade Level</th>
                                            <th>Section</th>
                                            <th>New Section</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($classes as $class): ?>
                                            <tr data-class-id="<?= htmlspecialchars($class['class_id']) ?>">
                                                <td><?= htmlspecialchars($class['grade_level']) ?></td>
                                                <td><?= htmlspecialchars($class['section']) ?></td>
                                                <td>
                                                    <select class="form-select section-dropdown" name="selected_sections[<?= htmlspecialchars($class['class_id']) ?>]">
                                                        <option value="" disabled selected>Select Section</option>
                                                        <?php foreach ($class['next_sections'] as $section): ?>
                                                            <option value="<?= htmlspecialchars($section) ?>"><?= htmlspecialchars($section) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                         
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        $('#promoteButton').click(function(event) {
            event.preventDefault(); // Prevent form submission
            let selectedSections = {};
            let sectionValues = [];
            let duplicateError = false;
            let missingSectionError = false;

            $('.section-dropdown').each(function() {
                let classId = $(this).closest('tr').data('class-id');
                let section = $(this).val();
                if (!section) {
                    missingSectionError = true;
                } else {
                    if (sectionValues.includes(section)) {
                        duplicateError = true;
                    }
                    sectionValues.push(section);
                    selectedSections[classId] = section;
                }
            });

            if (missingSectionError) {
                Swal.fire('Error!', 'Please select sections for all rows.', 'error');
                return;
            }

            if (duplicateError) {
                Swal.fire('Error!', 'Duplicate sections selected. Please choose unique sections.', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to promote students. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '',
                        data: {
                            promote_students: true,
                            selected_sections: selectedSections
                        },
                        success: function() {
                            Swal.fire('Success!', 'Students have been promoted.', 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred while promoting students.', 'error');
                        }
                    });
                }
            });
        });

        function handleSelectionChange(select) {
            const selectedValue = select.value;
            if (selectedValue) {
                window.location.href = selectedValue;
            }
        }
    </script>
</body>

</html>