<?php
// Get the user's position from the session
$userPosition = $_SESSION['position'] ?? '';

// Function to determine whether to show the Accounts section
$showAccounts = !in_array($userPosition, ['Elementary Chairperson', 'High School Chairperson', 'Teacher']);

// Function to determine whether to show the Classes section
$showClasses = !in_array($userPosition, ['Teacher']);

// Academic Years section is hidden for all roles, so no need for a function
$showAcademicYears = false;  // Set to false to hide it
?>

<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    
    <!-- Dashboard Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo $activePage === 'dashboard' ? '' : 'collapsed'; ?>" href="<?php echo $userPosition == 'Teacher' ? 'teacher-dashboard.php' : 'dashboard.php'; ?>">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

   <!-- Students Nav -->
<li class="nav-item">
  <a class="nav-link <?php echo in_array($activePage, ['add-student', 'attendance', 'student-list']) ? '' : 'collapsed'; ?>" data-bs-target="#students-nav" data-bs-toggle="collapse" href="#">
    <i class="bi bi-journal-text"></i><span>Students</span><i class="bi bi-chevron-down ms-auto"></i>
  </a>
  <ul id="students-nav" class="nav-content collapse <?php echo in_array($activePage, ['add-student', 'attendance', 'student-list']) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
    
    <?php
    // Show Student Registration only if user is not a teacher
    if ($userPosition != 'Teacher') {
    ?>
      <li>
        <a href="add-student.php" class="<?php echo $activePage === 'add-student' ? 'active' : ''; ?>">
          <i class="bi bi-circle"></i><span>Student Registration</span>
        </a>
      </li>
    <?php
    }
    ?>

    <li>
      <a href="<?php echo $userPosition == 'Teacher' ? 'teacher-student-list.php' : 'student-list.php'; ?>" class="<?php echo $activePage === 'student-list' ? 'active' : ''; ?>">
        <i class="bi bi-circle"></i><span>Student Management</span>
      </a>
    </li>

    <li>
      <a href="<?php echo $userPosition == 'Teacher' ? 'teacher-attendance.php' : 'attendance.php'; ?>" class="<?php echo $activePage === 'attendance' ? 'active' : ''; ?>">
        <i class="bi bi-circle"></i><span>Student Attendance</span>
      </a>
    </li>
  </ul>
</li><!-- End Students Nav -->


    <!-- Classes Nav (hidden for Teacher role) -->
    <?php if ($showClasses): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo in_array($activePage, ['class', 'class-management']) ? '' : 'collapsed'; ?>" data-bs-target="#classes-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-building"></i><span>Classes</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="classes-nav" class="nav-content collapse <?php echo in_array($activePage, ['class', 'class-management']) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo $userPosition == 'Teacher' ? 'teacher-class.php' : 'class.php'; ?>" class="<?php echo $activePage === 'class' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Class Allocation</span>
          </a>
        </li>
        <li>
          <a href="<?php echo $userPosition == 'Teacher' ? 'teacher-class-management.php' : 'class-management.php'; ?>" class="<?php echo $activePage === 'class-management' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Class Management</span>
          </a>
        </li>
      </ul>
    </li><!-- End Classes Nav -->
    <?php endif; ?>

 <!-- Reports Nav -->
<li class="nav-item">
  <?php if ($userPosition == 'Teacher'): ?>
    <!-- Show dropdown for Teacher -->
    <a class="nav-link <?php echo in_array($activePage, ['generate-report', 'template']) ? '' : 'collapsed'; ?>" href="#" data-bs-toggle="collapse" data-bs-target="#teacher-reports-nav" aria-expanded="<?php echo in_array($activePage, ['generate-report', 'template']) ? 'true' : 'false'; ?>">
      <i class="bi bi-calendar"></i><span>Reports</span>
      <i class="bi bi-chevron-down ms-auto"></i> <!-- Arrow for dropdown -->
    </a>
    <ul id="teacher-reports-nav" class="nav-content collapse <?php echo in_array($activePage, ['generate-report', 'template']) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
      <li>
        <a href="teacher-report.php" class="<?php echo $activePage === 'generate-report' ? 'active' : ''; ?>">
          <i class="bi bi-circle"></i><span>Generate Report</span>
        </a>
      </li>
      <li>
        <a href="teacher-report-template.php" class="<?php echo $activePage === 'template' ? 'active' : ''; ?>">
          <i class="bi bi-circle"></i><span>Report Template</span>
        </a>
      </li>
    </ul>
  <?php else: ?>
    <!-- Show single link for other users -->
    <a class="nav-link <?php echo $activePage === 'report' ? 'active' : 'collapsed'; ?>" href="report.php">
      <i class="bi bi-calendar"></i><span>Reports</span>
    </a>
  <?php endif; ?>
</li><!-- End Reports Nav -->


    <!-- Academic Years Nav (hidden for all roles) -->
    <?php if ($showAcademicYears): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo $activePage === 'manage-years' ? '' : 'collapsed'; ?>" href="<?php echo $userPosition == 'Teacher' ? 'teacher-manage-years.php' : 'manage-years.php'; ?>">
        <i class="bi bi-calendar"></i><span>Academic Years</span>
      </a>
    </li><!-- End Academic Years Nav -->
    <?php endif; ?>

    <!-- Accounts Nav (hidden for certain roles) -->
    <?php if ($showAccounts): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo in_array($activePage, ['account', 'add-teacher', 'administrator']) ? '' : 'collapsed'; ?>" data-bs-target="#teachers-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person"></i><span>Accounts</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="teachers-nav" class="nav-content collapse <?php echo in_array($activePage, ['account', 'add-teacher', 'administrator']) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="account.php" class="<?php echo $activePage === 'account' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Faculty</span>
          </a>
        </li>
        <li>
          <a href="administrator.php" class="<?php echo $activePage === 'administrator' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Administrators</span>
          </a>
        </li>
      </ul>
    </li><!-- End Accounts Nav -->
    <?php endif; ?>
    
  </ul>
</aside><!-- End Sidebar -->
