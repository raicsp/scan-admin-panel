<?php
$activePage = 'template'; // Set the active page
include 'database/db_connect.php';
include 'database/db-teacher-student-list.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');

// Directory where Excel files are stored
$excelDirectory = 'excel_templates/';
$excelFiles = array_diff(scandir($excelDirectory), array('..', '.')); // Get files excluding '.' and '..'

// Function to safely output filenames
function getFileUrl($fileName) {
    global $excelDirectory;
    return $excelDirectory . $fileName;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Faculty | Laboratory School | Batangas State University TNEU</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/bsu.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet"> <!-- Using simple-datatables CSS -->

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        /* Custom styling for underlined text */
        .download-link {
            text-decoration: underline;
            color: #007bff;
            font-weight: normal;
        }
        .container {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- ======= Header ======= -->
    <?php include 'header.php'; ?>
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <?php include 'sidebar.php'; ?>
    <!-- End Sidebar -->

    <main id="main" class="main">
    <div class="pagetitle">
        <h1>Report Templates</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="teacher-dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance Report Templates</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
        <div class="card">
        <h5 class="card-title">Attendance Report Templates</h5>
        <!-- Template Table -->
        <div class="container">
            <table id="templatesTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Template Name</th>
                        <th scope="col">Download Excel File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through the available Excel files and display them
                    foreach ($excelFiles as $file) {
                        $fileUrl = getFileUrl($file);
                        $fileName = pathinfo($file, PATHINFO_FILENAME); // Get the name without extension
                        echo "<tr>";
                        echo "<td>$fileName</td>"; // Display the template name
                        echo "<td><a href='$fileUrl' download class='download-link'>$fileName.xlsx</a></td>"; // Display as underlined text with .xlsx
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        </div>
    </main>
    

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Simple Datatables JS -->
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script> <!-- Include simple-datatables JS -->

    <script>
        // Initialize Simple Datatable for the template table
        const dataTable = new simpleDatatables.DataTable("#templatesTable", {
            perPage: 5, // Number of records per page
            perPageSelect: [5, 10, 20], // Options for records per page
            searchable: true, // Enable search functionality
            sortable: true // Enable sorting functionality
        });
    </script>

</body>

</html>
