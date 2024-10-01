<?php
$activePage = 'student-list';
include 'database/db_connect.php';
include 'database/db-student-list.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>SCAN</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    .clickable-row {
      cursor: pointer;
    }

    .clickable-row:hover {
      background-color: #f1f1f1;
    }

    .action-buttons {
      cursor: auto;
    }
  </style>
</head>

<body>
  <!-- ======= Header ======= -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidebar.php'; ?>
  <!-- End Sidebar-->
  <!-- Edit Student Modal -->
  <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editStudentForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editStudentModalLabel">Edit Student Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editStudentId" name="student_id">

          <!-- First row: Full Name - School Year -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="editStudentName" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="editStudentName" name="student_name" placeholder="John Doe" required maxlength="50" pattern="[a-zA-Z\s]+" title="Only letters are allowed, up to 50 characters">
            </div>
            <div class="col-md-6">
              <label for="editSchoolYear" class="form-label">School Year</label>
              <input type="text" name="school_year" class="form-control" id="editSchoolYear" placeholder="2024-2025" required>
            </div>
          </div>

          <!-- Second row: Gender, Grade Level, Section -->
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="editStudentGender" class="form-label">Gender</label>
              <select name="gender" id="editStudentGender" class="form-select" required>
                <option selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="editStudentGrade" class="form-label">Grade Level</label>
              <select name="grade_level" id="editStudentGrade" class="form-select" required>
                <option value="">Select Grade</option>
                <?php foreach ($grades as $grade) : ?>
                  <option value="<?= htmlspecialchars($grade) ?>"><?= htmlspecialchars($grade) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="editStudentSection" class="form-label">Section</label>
              <select name="section" id="editStudentSection" class="form-select" required>
                <option value="">Select Section</option>
                <?php foreach ($sections as $section) : ?>
                  <option value="<?= htmlspecialchars($section) ?>"><?= htmlspecialchars($section) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Third row: Parent Contact Number, Parent Email Address -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="editParentContact" class="form-label">Parent Contact Number</label>
              <input type="tel" name="parent_contact" class="form-control" id="editParentContact" placeholder="09123456789" required pattern="\d{11}" title="Enter exactly 11 digits" required maxlength="11">
            </div>
            <div class="col-md-6">
              <label for="editParentEmail" class="form-label">Parent Email Address</label>
              <input type="email" name="parent_email" class="form-control" id="editParentEmail" placeholder="parent@example.com" required maxlength="50" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Please enter a valid email address, up to 50 characters">
            </div>
          </div>

          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="editGridCheck" required>
              <label class="form-check-label" for="editGridCheck">Confirm information is accurate</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


  <!-- Confirm Delete Modal -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this class?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
        </div>
      </div>
    </div>
  </div><!-- End Confirm Delete Modal -->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Student List</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Data</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Student List</h5>
              <!-- Filter-->
              <div class="row mb-3">
                <div class="col-md-6">
                  <select id="gradeFilter" class="form-select">
                    <option value="">Select Grade</option>
                    <?php foreach ($grades as $grade) : ?>
                      <option value="<?= htmlspecialchars($grade) ?>"><?= htmlspecialchars($grade) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <select id="sectionFilter" class="form-select">
                    <option value="">Select Section</option>
                    <?php foreach ($sections as $section) : ?>
                      <option value="<?= htmlspecialchars($section) ?>"><?= htmlspecialchars($section) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
    <div class="col-md-6">
      <input type="text" id="searchBar" class="form-control" placeholder="Search by student name">
    </div>
  </div>
              <!-- Table with stripped rows -->
              <table id="studentsTable" class="table datatable table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($students as $student) : ?>
                    <tr class="clickable-row" data-name="<?= htmlspecialchars($student['student_name']) ?>">
                      <th scope="row"><?= htmlspecialchars($student['student_name']) ?></th>
                      <td><?= htmlspecialchars($student['grade_level']) ?></td>
                      <td><?= htmlspecialchars($student['section']) ?></td>
                      <td class="action-buttons">
                        <a href="#" class="btn btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editStudentModal" data-student='<?= json_encode($student) ?>'>
                          <i class="bx bx-edit-alt"></i>
                        </a>
                        <a href="#" class="btn btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-student-id="<?= $student['studentID'] ?>">
                          <i class="bi bi-trash"></i>
                        </a>
                      </td>


                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Filter script -->
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const gradeFilter = document.getElementById('gradeFilter');
      const sectionFilter = document.getElementById('sectionFilter');
      const table = document.getElementById('studentsTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      const filterTable = () => {
        const gradeValue = gradeFilter.value;
        const sectionValue = sectionFilter.value;

        for (let row of rows) {
          const grade = row.cells[1].textContent.trim(); // Grade is in the 2nd column (index 1)
          const section = row.cells[2].textContent.trim(); // Section is in the 3rd column (index 2)

          if ((gradeValue === "" || grade === gradeValue) && (sectionValue === "" || section === sectionValue)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      };
      const searchTable = () => {
        const searchValue = searchBar.value.toLowerCase();

        for (let row of rows) {
          const studentName = row.cells[0].textContent.trim().toLowerCase(); // Student name is in the 1st column (index 0)
          if (studentName.includes(searchValue)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      };

      searchBar.addEventListener('input', searchTable);

      gradeFilter.addEventListener('change', filterTable);
      sectionFilter.addEventListener('change', filterTable);
    });

    document.addEventListener('DOMContentLoaded', (event) => {
      const table = document.getElementById('studentsTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        row.classList.add('clickable-row');
        row.addEventListener('click', function(event) {
          if (!event.target.closest('.action-buttons')) {
            const studentName = row.getAttribute('data-name');
            window.location.href = `student-details.php?name=${encodeURIComponent(studentName)}`;
          }
        });
      }
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {


      const editButtons = document.querySelectorAll('.edit-btn');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const student = JSON.parse(this.getAttribute('data-student'));
          document.getElementById('editStudentId').value = student.studentID;
          document.getElementById('editStudentName').value = student.student_name;
          document.getElementById('editStudentGender').value = student.gender;
          document.getElementById('editStudentGrade').value = student.grade_level;
          document.getElementById('editStudentSection').value = student.section;
          document.getElementById('editParentContact').value = student.parent_contact;
          document.getElementById('editParentEmail').value = student.parent_email;
          document.getElementById('editSchoolYear').value = student.school_year;
        });
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const deleteButtons = document.querySelectorAll('.delete-btn');
      deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
          const studentId = this.getAttribute('data-student-id');
          const confirmDeleteButton = document.getElementById('confirmDeleteButton');
          confirmDeleteButton.setAttribute('data-student-id', studentId);
        });
      });

      const confirmDeleteButton = document.getElementById('confirmDeleteButton');
      confirmDeleteButton.addEventListener('click', function() {
        const studentId = this.getAttribute('data-student-id');
        // Add the code to handle the deletion of the student using the studentId
        console.log('Student ID to delete:', studentId);
        // Make an AJAX request or form submission to delete the student
      });
    });
  </script>

</body>

</html>