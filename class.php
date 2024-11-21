<?php
$activePage = 'class';
include 'database/db_connect.php';
include 'database/db-class.php';
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
  <link href="assets/img/bsu.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
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
      <h1>Class Allocation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Class Allocation</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">List of Classes</h5>

              <!-- Filters -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <select id="gradeFilter" class="form-select">
                    <option value="">Select Grade</option>
                    <?php foreach ($grades as $grade) : ?>
                      <option value="<?php echo htmlspecialchars($grade); ?>"><?php echo htmlspecialchars($grade); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <select id="sectionFilter" class="form-select">
                    <option value="">Select Section</option>
                    <!-- Section options will be populated dynamically -->
                  </select>
                </div>
              </div>

              <!-- DataTable -->
              <table id="classTable" class="table display">
                <thead>
                  <tr>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($classes as $class) : ?>
                    <tr>
                      <td><?php echo htmlspecialchars($class['grade_level']); ?></td>
                      <td><?php echo htmlspecialchars($class['section']); ?></td>
                      <td>
                        <select class="form-select" data-class-id="<?php echo htmlspecialchars($class['class_id']); ?>" onchange="updateTeacher(this)">
                          <option value="">Assign Teacher</option>
                          <?php foreach ($teachers as $teacher) : ?>
                            <option value="<?php echo htmlspecialchars($teacher['id']); ?>" <?php echo ($class['assigned_teacher_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($teacher['fullname']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Vendor JS Files -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Main JS -->
  <script>
  $(document).ready(function () {
    // Custom sorting function for numeric values in "Grade Level"
    jQuery.fn.dataTable.ext.type.order['grade-level-pre'] = function (data) {
      const match = data.match(/\d+/); // Extract the numeric value from the grade level
      return match ? parseInt(match[0], 10) : 0; // Return the numeric value for sorting
    };

    // Initialize DataTable
    const table = $('#classTable').DataTable({
      paging: true,
      searching: true,
      ordering: true,
      pageLength: 10,
      order: [[0, 'asc']], // Default sorting on the Grade Level column (index 0)
      columnDefs: [
        { type: 'grade-level', targets: 0 }, // Apply custom sorting to Grade Level column
      ],
      language: {
        search: "Filter records:",
        lengthMenu: "Show _MENU_ entries per page",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "No matching entries",
        paginate: {
          previous: "Previous",
          next: "Next"
        }
      },
    });

    // Section Data from PHP
    const sectionsByGrade = <?php echo json_encode($sectionsByGrade); ?>;

    // Populate Sections Based on Grade Selection
    $('#gradeFilter').on('change', function () {
      const selectedGrade = $(this).val();
      const sectionFilter = $('#sectionFilter');

      // Clear existing options
      sectionFilter.empty().append('<option value="">Select Section</option>');

      // Populate new options if a grade is selected
      if (selectedGrade && sectionsByGrade[selectedGrade]) {
        sectionsByGrade[selectedGrade].forEach(section => {
          sectionFilter.append(new Option(section, section));
        });
      }

      // Trigger filtering
      $('#sectionFilter').trigger('change');
    });

    // Custom Filters
    $('#gradeFilter, #sectionFilter').on('change', function () {
      const grade = $('#gradeFilter').val();
      const section = $('#sectionFilter').val();

      // Apply exact match search using regex
      table
        .columns(0).search(grade ? `^${grade}$` : '', true, false) // Grade Level column (index 0)
        .columns(1).search(section ? `^${section}$` : '', true, false) // Section column (index 1)
        .draw();
    });
  });

  // Update teacher functionality
  function updateTeacher(selectElement) {
    const classId = selectElement.getAttribute('data-class-id');
    const teacherId = selectElement.value;

    if (!teacherId) {
      alert("Please select a teacher.");
      return;
    }

    if (confirm("Are you sure you want to assign this teacher?")) {
      $.post('database/db-class.php', {
        class_id: classId,
        teacher_id: teacherId
      }).done(function (response) {
        if (response.trim() === 'success') {
          alert("Teacher assigned successfully.");
          location.reload();
        } else {
          alert("Failed to assign teacher.");
        }
      }).fail(function () {
        alert("Error occurred while assigning teacher.");
      });
    }
  }
</script>


</body>

</html>
