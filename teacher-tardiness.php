<?php
include 'database/db_connect.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');
$class_id = $_SESSION['class_id'] ?? '';

// Set default values for month filter
$selectedMonth = $_GET['month'] ?? '';

// Build the query to get students with their total late count
$late_sql = "
SELECT s.studentID, s.srcode, 
       s.name AS student_name, 
       c.grade_level, c.section,
       (SELECT COUNT(*) 
        FROM attendance a2 
        WHERE a2.studentID = s.studentID 
          AND a2.status = 'Late' ";

// If a month filter is applied, filter the attendance records by that month
if ($selectedMonth) {
    $late_sql .= " AND DATE_FORMAT(a2.date, '%Y-%m') = '$selectedMonth'";
}

$late_sql .= "
       ) AS late_count
FROM student s
JOIN classes c ON s.class_id = c.class_id
WHERE s.class_id = '$class_id'
";

// Only apply the HAVING clause if there is a specific month selected (to show only students with lateness)
if ($selectedMonth) {
    $late_sql .= " HAVING late_count > 0";
}

$late_sql .= " ORDER BY late_count DESC";

$result = $conn->query($late_sql);

// Check if there are any results and store them in an array
$absentStudents = [];
if ($result && $result->num_rows > 0) {
    $absentStudents = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
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
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Make table rows hoverable */
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
            /* Adjust this color as needed */
        }

        /* Make the cursor a pointer when hovering over rows */
        .table-hover tbody tr {
            cursor: pointer;
        }
    </style>

</head>

<body>

    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Students with Most Frequent Tardiness</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="teacher-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Most Frequent Tardiness</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mt-4">Students with Most Frequent Tardiness</h5>
                            <form id="filterForm" class="row g-3">
                                <div class="col-md-5">
                                    <label for="monthFilter" class="form-label">Select Month</label>
                                    <input type="month" class="form-control" id="monthFilter" name="month" value="<?= htmlspecialchars($selectedMonth) ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary" id="filterButton">Filter</button>
                                </div>
                            </form>



                            <table class="table table-bordered table-hover" id="absenceTable">
                                <thead>
                                    <tr>
                                        <th>Sr-Code</th>
                                        <th>Name</th>
                                        <th>Tardiness</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($absentStudents as $student) : ?>
                                        <tr data-name="<?= htmlspecialchars($student['srcode']) ?>">
                                            <td><?= htmlspecialchars($student['srcode']) ?></td>
                                            <td><?= htmlspecialchars($student['student_name']) ?></td>
                                            <td><?= htmlspecialchars($student['late_count']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
        // Initialize the DataTable
        const dataTable = new simpleDatatables.DataTable("#absenceTable", {
            searchable: true,
            paging: true,
            perPage: 10,
        });

        // Filter button event
        document.getElementById('filterButton').addEventListener('click', function() {
            const selectedMonth = document.getElementById('monthFilter').value; // Get the selected month value
            const url = new URL(window.location.href);

            // If no month is selected, remove the 'month' parameter from the URL to show all records
            if (!selectedMonth) {
                url.searchParams.delete('month');
            } else {
                url.searchParams.set('month', selectedMonth); // Add selected month to URL
            }

            window.location.href = url.toString(); // Reload page with the updated URL
        });


        // Add click functionality to table rows
        document.addEventListener('DOMContentLoaded', (event) => {
            const table = document.getElementById('absenceTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let row of rows) {
                row.classList.add('clickable-row');
                row.addEventListener('click', function(event) {
                    if (!event.target.closest('.action-buttons')) {
                        const studentName = row.getAttribute('data-name');
                        window.location.href = `student-details.php?srcode=${encodeURIComponent(studentName)}`;
                    }
                });
            }
        });
    </script>
</body>

</html>
