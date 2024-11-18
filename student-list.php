<?php
$activePage = 'student-list'; // Set the active page
include 'database/db_connect.php';
include 'database/db-student-list.php';
session_start();

$userPosition = trim($_SESSION['position'] ?? '');
echo "<script>console.log('User Position: " . addslashes($userPosition) . "');</script>";

// Define grade conditions based on user position
$gradeCondition = '';
if ($userPosition === 'Elementary Chairperson') {
  // Allow access only to Kinder to Grade-6
  $gradeCondition = "WHERE c.grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
} elseif ($userPosition === 'High School Chairperson') {
  // Allow access only to Grade-7 to Grade-12
  $gradeCondition = "WHERE c.grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
}

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

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i"
    rel="stylesheet">

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
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Student Information Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Student Information Management</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">

            <h5 class="card-title">Manage Student Details and Records</h5>



              <!-- Filter by Grade Level and Section -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <select id="filterGrade" class="form-select">
                    <option value="">Filter by Grade</option>
                    <?php
                    // Generate grade options from the database based on user position
                    $gradeConditionForDropdown = '';
                    if ($userPosition === 'Elementary Chairperson') {
                      $gradeConditionForDropdown = "WHERE grade_level IN ('Kinder', 'Grade-1', 'Grade-2', 'Grade-3', 'Grade-4', 'Grade-5', 'Grade-6')";
                    } elseif ($userPosition === 'High School Chairperson') {
                      $gradeConditionForDropdown = "WHERE grade_level IN ('Grade-7', 'Grade-8', 'Grade-9', 'Grade-10', 'Grade-11', 'Grade-12')";
                    }

                    // Generate grade options from the database
                    $gradeQuery = $conn->query("SELECT DISTINCT grade_level FROM classes $gradeConditionForDropdown");
                    while ($gradeRow = $gradeQuery->fetch_assoc()) {
                      echo "<option value='{$gradeRow['grade_level']}'>{$gradeRow['grade_level']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <select id="filterSection" class="form-select">
                    <option value="">Filter by Section</option>
                  </select>
                </div>
              </div>



              <!-- <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name">
              </div>
 -->

              <!-- Table with Data -->
              <table id="studentsTable" class="table">
                <thead>
                  <tr>
                    <th>SrCode</th>
                    <th>Name</th>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Fetch the students with the grade level restriction based on the user’s role
                  $query = "
              SELECT 
        s.studentID,
        s.srcode,
        s.name AS student_name,
        s.gender,
        c.grade_level,
        c.section,
        s.parent_contact,
        s.gmail AS parent_email,
        s.school_year
    FROM 
        student s
    JOIN 
        classes c ON s.class_id = c.class_id
    $gradeCondition
    ORDER BY 
        CASE 
            WHEN c.grade_level = 'Kinder' THEN 1
            WHEN c.grade_level = 'Grade-1' THEN 2
            WHEN c.grade_level = 'Grade-2' THEN 3
            WHEN c.grade_level = 'Grade-3' THEN 4
            WHEN c.grade_level = 'Grade-4' THEN 5
            WHEN c.grade_level = 'Grade-5' THEN 6
            WHEN c.grade_level = 'Grade-6' THEN 7
            WHEN c.grade_level = 'Grade-7' THEN 8
            WHEN c.grade_level = 'Grade-8' THEN 9
            WHEN c.grade_level = 'Grade-9' THEN 10
            WHEN c.grade_level = 'Grade-10' THEN 11
            WHEN c.grade_level = 'Grade-11' THEN 12
            WHEN c.grade_level = 'Grade-12' THEN 13
            ELSE 14
        END,
        s.name ASC
        ";

                  $result = $conn->query($query);

                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr class='clickable-row' data-name='" . htmlspecialchars($row['srcode']) . "'>
                        <td>{$row['srcode']}</td>
                        <td>{$row['student_name']}</td>
                        <td>{$row['grade_level']}</td>
                        <td>{$row['section']}</td>
                        <td class='action-buttons'>
                            <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editStudentModal'
                                onclick=\"editStudent(
                                    '{$row['studentID']}',
                                    '{$row['srcode']}',
                                    '{$row['student_name']}', 
                                    '{$row['school_year']}', 
                                    '{$row['gender']}', 
                                    '{$row['grade_level']}', 
                                    '{$row['section']}', 
                                    '{$row['parent_contact']}', 
                                    '{$row['parent_email']}'
                                )\">Edit</button>
                            <button class='btn btn-danger btn-sm' onclick='deleteStudent({$row['studentID']})'>Delete</button>
                        </td>
                    </tr>";
                    }
                  }
                  ?>
                </tbody>
              </table>
              <!-- End Table -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Edit Student Modal ======= -->

  <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editStudentForm" class="row g-3" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
          <input type="hidden" id="editStudentId" name="id">
          <input type="hidden" name="edit_student" value="1">

          <div class="col-md-6">
            <label for="editSRCode" class="form-label">SR-Code</label>
            <input type="text" class="form-control" id="editSRCode" name="srcode" required maxlength="20" readonly>
          </div>

          <div class="col-md-6">
            <label for="editFullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="editFullName" name="full_name" required maxlength="50" pattern="[a-zA-Z\s,\.]+" title="Only letters are allowed, up to 50 characters">
          </div>

          <div class="col-md-6">
            <label for="editSchoolYear" class="form-label">School Year</label>
            <input type="text" class="form-control" id="editSchoolYear" name="school_year" required>
          </div>

          <div class="col-md-6">
            <label for="editGender" class="form-label">Gender</label>
            <select class="form-control" id="editGender" name="gender" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="editGradeLevel" class="form-label">Grade Level</label>
            <select class="form-control" id="editGradeLevel" name="grade_level" required>
              <?php
              // Fetch available grade levels from the 'classes' table
              $gradeResult = $conn->query("SELECT DISTINCT grade_level FROM classes");
              if ($gradeResult->num_rows > 0) {
                while ($gradeRow = $gradeResult->fetch_assoc()) {
                  echo "<option value='{$gradeRow['grade_level']}'>{$gradeRow['grade_level']}</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-md-6">
            <label for="editSection" class="form-label">Section</label>
            <select class="form-control" id="editSection" name="section" required>
              <option value="">Select Section</option> <!-- Default Option -->
            </select>
          </div>

          <div class="col-md-6">
            <label for="editParentContact" class="form-label">Parent Contact Number</label>
            <input type="text" class="form-control" placeholder="9876543210" id="editParentContact" name="parent_contact" required pattern="\d{10}" maxlength="10" title="Enter exactly 10 digits">
          </div>

          <div class="col-md-6">
            <label for="editParentEmail" class="form-label">Parent Email Address</label>
            <input type="email" class="form-control" id="editParentEmail" name="parent_email" required maxlength="50" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Please enter a valid email address, up to 50 characters">
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form><!-- End Edit Student Form -->
        
        <!-- Hidden Delete Form -->
        <form id="deleteStudentForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
          <input type="hidden" name="delete_student" value="1">
          <input type="hidden" id="deleteStudent" name="id">
        </form>
      </div>
    </div>
  </div>
</div><!-- End Edit Student Modal-->


  <script>
       document.getElementById('editGradeLevel').addEventListener('change', function() {
      const selectedGrade = this.value;
      const sectionDropdown = document.getElementById('editSection');

      // Clear current sections
      sectionDropdown.innerHTML = '<option value="">Select Section</option>';

      // Check if sections exist for the selected grade level
      if (gradeSections[selectedGrade]) {
        gradeSections[selectedGrade].forEach(function(section) {
          const option = document.createElement('option');
          option.value = section;
          option.textContent = section;
          sectionDropdown.appendChild(option);
        });
      }
    });
   
    // This function populates the fields when the modal opens
    function editStudent(id, srcode, name, schoolYear, gender, gradeLevel, section, parentContact, parentEmail) {
      document.getElementById('editStudentId').value = id;
      document.getElementById('editSRCode').value = srcode;
      document.getElementById('editFullName').value = name;
      document.getElementById('editSchoolYear').value = schoolYear;
      document.getElementById('editGender').value = gender;
      document.getElementById('editParentContact').value = parentContact;
      document.getElementById('editParentEmail').value = parentEmail;

      // Set grade level and trigger change event to update sections
      const gradeDropdown = document.getElementById('editGradeLevel');
      gradeDropdown.value = gradeLevel;
      gradeDropdown.dispatchEvent(new Event('change')); // Trigger change event

      // Set the section
      document.getElementById('editSection').value = section;
    }
  </script>



  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Custom JS to Populate Edit Modal with Data -->
  <script>

    //clicable row
    document.addEventListener('DOMContentLoaded', (event) => {
    const table = document.getElementById('studentsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let row of rows) {
        row.classList.add('clickable-row');
        row.addEventListener('click', function(event) {
            // Check if the click is not on an action button
            if (!event.target.closest('.action-buttons')) {
                // Get the srcode (not the formatted name) from the data-name attribute
                const studentSrCode = row.getAttribute('data-name');
                // Redirect to the student details page, passing srcode as the query parameter
                window.location.href = `student-details.php?srcode=${encodeURIComponent(studentSrCode)}`;
            }
        });
    }
});



    function deleteStudent(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          // Send a delete request
          document.getElementById('deleteStudent').value = id;
          document.getElementById('deleteStudentForm').submit();
        }
      });
    }

    // Function to display success notification after deletion
    <?php if (isset($response['success']) && $response['success'] === true && isset($_POST['delete_student'])): ?>
      Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: 'Student has been deleted successfully.',
        confirmButtonText: 'OK'
      });
    <?php endif; ?>

    // Function to display success notification after editing
    <?php if (isset($response['success']) && $response['success'] === true && isset($_POST['edit_student'])): ?>
      Swal.fire({
        icon: 'success',
        title: 'Updated!',
        text: 'Student details have been updated successfully.',
        confirmButtonText: 'OK'
      });
    <?php endif; ?>

    // Search functionality
    // document.getElementById('searchInput').addEventListener('input', function() {
    //   const filter = this.value.toUpperCase();
    //   const rows = document.querySelectorAll('#studentsTable tbody tr');
    //   rows.forEach(row => {
    //     const name = row.cells[0].textContent.toUpperCase();
    //     row.style.display = name.includes(filter) ? '' : 'none';
    //   });
    // });

  
  </script>
  <script>
// Ensure that these variables are properly defined
const gradeFilter = document.getElementById('filterGrade');
const sectionFilter = document.getElementById('filterSection');
const searchInput = document.getElementById('searchInput');

// Initialize DataTable
const dataTable = new simpleDatatables.DataTable("#studentsTable", {
    searchable: true, // Enable default search input
    paging: true,
    fixedHeight: true,
    perPage: 10,
    labels: {
        placeholder: "Search...",
        perPage: "entries per page",
        noRows: "No results found",
        info: "Showing {start} to {end} of {rows} results"
    }
});

// When the grade dropdown changes, update the section options
gradeFilter.addEventListener('change', () => {
    const selectedGrade = gradeFilter.value;
    sectionFilter.innerHTML = '<option value="">Filter by Section</option>'; // Clear previous sections

    if (selectedGrade && gradeSections[selectedGrade]) {
        gradeSections[selectedGrade].forEach(section => {
            const option = document.createElement('option');
            option.value = section;
            option.textContent = section;
            sectionFilter.appendChild(option);
        });
    }

    filterTable(); // Apply filter after changing grade
});

// Filter table based on search and selection
function filterTable() {
    const gradeFilterValue = gradeFilter.value.toUpperCase();
    const sectionFilterValue = sectionFilter.value.toUpperCase();
 //   const searchQuery = searchInput.value.toUpperCase();

    // Build filter query based on selected values
    let filterQuery = '';

    // Append grade filter
    if (gradeFilterValue) {
        filterQuery += gradeFilterValue;
    }

    // Append section filter
    if (sectionFilterValue) {
        filterQuery += ' ' + sectionFilterValue; // Add space to separate terms
    }

    // // Append search query for name
    // if (searchQuery) {
    //     filterQuery += ' ' + searchQuery; // Add space to separate terms
    // }

    // Apply the search/filter on the datatable
    dataTable.search(filterQuery.trim()); // Use search method to filter the table
}

// Event listeners for filtering
gradeFilter.addEventListener('change', filterTable);
//sectionFilter.addEventListener('change', filterTable);
searchInput.addEventListener('input', filterTable);

// Clickable row functionality
 
  </script>

</body>

</html>
