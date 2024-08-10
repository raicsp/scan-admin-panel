<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <!-- Dashboard Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo $activePage === 'dashboard' ? '' : 'collapsed'; ?>" href="dashboard.php">
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
        <li>
          <a href="add-student.php" class="<?php echo $activePage === 'add-student' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Register Student</span>
          </a>
        </li>
        <li>
          <a href="attendance.php" class="<?php echo $activePage === 'attendance' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Student Attendance</span>
          </a>
        </li>
        <li>
          <a href="student-list.php" class="<?php echo $activePage === 'student-list' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Student List</span>
          </a>
        </li>
      </ul>
    </li><!-- End Students Nav -->

    <!-- Teachers Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo in_array($activePage, ['account', 'register-teacher']) ? '' : 'collapsed'; ?>" data-bs-target="#teachers-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person"></i><span>Teachers</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="teachers-nav" class="nav-content collapse <?php echo in_array($activePage, ['account', 'add-teacher']) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="account.php" class="<?php echo $activePage === 'account' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Accounts</span>
          </a>
        </li>
        <li>
          <a href="add-teacher.php" class="<?php echo $activePage === 'add-teacher' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Register Teacher</span>
          </a>
        </li>
      </ul>
    </li><!-- End Teachers Nav -->

    <!-- Classes Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo in_array($activePage, ['class', 'class-management']) ? '' : 'collapsed'; ?>" data-bs-target="#classes-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-building"></i><span>Classes</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="classes-nav" class="nav-content collapse <?php echo in_array($activePage, ['class', 'class-management']) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="class.php" class="<?php echo $activePage === 'class' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Class Allocation</span>
          </a>
        </li>
        <li>
          <a href="class-management.php" class="<?php echo $activePage === 'class-management' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Manage Classes</span>
          </a>
        </li>
      </ul>
    </li><!-- End Classes Nav -->

        <!-- Reports Nav -->
        <li class="nav-item">
      <a class="nav-link <?php echo in_array($activePage, ['attendance-report', 'performance-report']) ? '' : 'collapsed'; ?>" data-bs-target="#reports-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="reports-nav" class="nav-content collapse <?php echo in_array($activePage, ['attendance-report', 'performance-report']) ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="report.php" class="<?php echo $activePage === 'attendance-report' ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i><span>Attendance Report</span>
          </a>
        </li>

      </ul>
    </li><!-- End Reports Nav -->

  </ul>

     
</aside><!-- End Sidebar -->