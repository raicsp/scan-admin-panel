<?php
$activePage = 'dashboard';
include 'database/db_connect.php'; // Include the database connection
include 'database/db-dashboard.php';
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


  <!-- Chart Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <style>
    .equal-height {
      display: flex;
      flex-direction: column;
    }
   
  </style>
</head>

<body>
  <!-- Header -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidebar.php'; ?>
  <!-- End Sidebar-->

  <!-- ======= Main ======= -->
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- 1st Layer: 3 Cards -->
        <div class="col-12">
          <div class="row">
            

            <div class="col-md-4">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Present Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="present-today"><?php echo $present_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Present Today Card -->

            <div class="col-md-4">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Absent Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-x-circle"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="absent-today"><?php echo $absent_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Absent Today Card -->
            <div class="col-md-4">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Late Arrivals Today</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-clock"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="late-today"><?php echo $late_today; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Students Card -->
          </div>
        </div><!-- End 1st Layer -->

        <!-- 2nd Layer: 2 Cards -->
        <div class="col-12">
          <div class="row">
            <div class="col-md-6">
              <div class="card info-card sales-card">
              <div class="card-body">
                  <h5 class="card-title">Total Number of Students</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="student"><?php echo $student; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Students</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Late Students Card -->

            <div class="col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Total Number of Users</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="teacher"><?php echo $teacher; ?></h6>
                      <span class="text-muted small pt-2 ps-1">Teachers</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Total Teachers Card -->
          </div>
        </div><!-- End 2nd Layer -->

       

            

        <div class="col-md-6 equal-height">
          <div class="card">
            <div class="filter">
            </div>
            <div class="card-body">
              <h5 class="card-title">Gender Distribution</h5>
              <div id="genderDistributionChart"></div>
            </div>
          </div>
        </div><!-- End Gender Distribution Chart -->
        
        <div class="col-md-6 equal-height">
  <div class="card">
    <div class="filter">
      <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <li class="dropdown-header text-start">
          <h6>Filter</h6>
        </li>
        <li>
          <select id="gradeFilter" class="form-select">
            <option value="">Select Grade</option>
            <?php foreach ($grades as $grade_level) : ?>
              <option value="<?= htmlspecialchars($grade_level) ?>" <?= ($grade_level == $grade) ? 'selected' : '' ?>><?= htmlspecialchars($grade_level) ?></option>
            <?php endforeach; ?>
          </select>
        </li>
        <li>
          <select id="timeFilter" class="form-select">
            <option value="today" <?= ($filter == 'today') ? 'selected' : '' ?>>Today</option>
            <option value="week" <?= ($filter == 'week') ? 'selected' : '' ?>>This Week</option>
            <option value="month" <?= ($filter == 'month') ? 'selected' : '' ?>>This Month</option>
          </select>
        </li>
        <li>
          <div class="calendar-filter">
            <input type="text" id="calendarFilter" class="form-control" placeholder="Select Date" />
          </div>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <h5 class="card-title">
        Attendance Distribution<span> | <?= ucfirst($filter) ?> <?= $grade ? "$grade" : "" ?></span>
      </h5>
      <div id="attendanceSummaryChart"></div>
    </div>
  </div>
</div><!-- End Attendance Summary Chart -->

         <!-- 3rd Layer: Attendance Overview and Attendance by Grade -->
         <div class="col-12">
          <div class="row">
            <div class="col-md-6 equal-height">
              <div class="card">
                <div class="card-body">
                  <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                      <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                      </li>

                      <li>
                        <select id="monthFilter" class="form-select">
                          <option value="">Select Month</option>
                          <?php foreach ($month_options as $month) : ?>
                            <option value="<?= htmlspecialchars($month['month']) ?>" <?= ($month['month'] == $selectedMonth) ? 'selected' : '' ?>>
                              <?= htmlspecialchars($month['month_name']) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </li>
                    </ul>
                  </div>
                  <h5 class="card-title"> Monthly Attendance Overview <span>| <?= htmlspecialchars($formattedMonth) ?></span></h5>
                  <div id="attendance-line-chart"></div>
                </div>
              </div>
            </div> <!--End Attendance Overview Line Chart -->

       
        
        <div class="col-md-6 equal-height">
          <div class="card">
            <div class="card-body">
              <div class="filter" style=" display: flex; align-items: center;">
                <!-- Calendar Icon -->
                <a class="calendar-icon" href="#" style="margin-right: 20px; padding-top: 8px;">
                  <i class="bi bi-calendar"></i>
                </a> 
                <input type="text" id="calendarFilter" class="form-control" placeholder="Select Date" 
                      style="display: none; width: 100px;  font-size: 11px;  border: none;  outline: none; padding-right: 20px;" />
              </div>

              <h5 id="cardTitle" class="card-title mt-3" style=" text-align: left; padding: 30px; padding-top: 5px;">Attendance Overview By Grade</h5>
              <div id="byGradeChart"></div>
            </div>
          </div>
        </div><!-- End Attendance By Grade -->
           

        <!-- 5th Layer: Top Students with Most Absences and Top Students with Most Late -->
        
          <div class="col-md-6 equal-height">
              <div class="card top-students overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="perfect-attendance.php">View All</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Students with Perfect Attendance </h5>
                  <table class="table table-hover" id="perfectAttendanceTable">
                    <thead>
                      <tr>
                      <th scope="col">Sr-Code</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Grade</th>
                        <th scope="col">Section</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $perfect_attendance_result->fetch_assoc()) : ?>
                        <tr class="clickable-row" data-name="<?= htmlspecialchars($row['srcode']) ?>">
                        <td><?php echo $row['srcode']; ?></td>
                          <td><?php echo $row['student_name']; ?></td>
                          <td><?php echo $row['grade_level']; ?></td>
                          <td><?php echo $row['section']; ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div><!-- End Top Students with Most Late -->
            <div class="col-md-6 equal-height">
              <div class="card top-students overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li><a class="dropdown-item" href="most-frequent-absences.php">View All</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Students with Most Frequent Absences </h5>
                  <table class="table table-hover" id="absencesTable">
                    <thead>
                      <tr>
                      <th scope="col">Sr-Code</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Grade</th>
                        <th scope="col">Section</th>
                        <th scope="col">Absences</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $absences_result->fetch_assoc()) : ?>
                        <tr class="clickable-row" data-name="<?= htmlspecialchars($row['srcode']) ?>">
                        <td><?php echo $row['srcode']; ?></td>
                          <td><?php echo $row['student_name']; ?></td>
                          <td><?php echo $row['grade_level']; ?></td>
                          <td><?php echo $row['section']; ?></td>
                          <td class="fw-bold"><?php echo $row['absence_count']; ?></td>
                         
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Top Students with Most Absences -->

            <div class="col-md-12 equal-height">
              <div class="card top-students overflow-auto">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="most-frequent-tardiness.php">View All</a></li>
                  </ul>
                </div>
                <div class="card-body pb-0">
                  <h5 class="card-title">Students with Most Frequent Late Arrivals </h5>
                  <table class="table table-hover" id="lateTable">
                    <thead>
                      <tr>
                      <th scope="col">Sr-Code</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Grade</th>
                        <th scope="col">Section</th>
                        <th scope="col">Lates</th>
                    
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $late_result->fetch_assoc()) : ?>
                        <tr class="clickable-row" data-name="<?= htmlspecialchars($row['srcode']) ?>">
                        <td><?php echo $row['srcode']; ?></td>
                          <td><?php echo $row['student_name']; ?></td>
                          <td><?php echo $row['grade_level']; ?></td>
                          <td><?php echo $row['section']; ?></td>
                          <td class="fw-bold"><?php echo $row['late_count']; ?></td>
                         
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Top Students with Most Late -->
            
          </div>
        </div><!-- End 5th Layer -->

      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Chart Data from PHP -->
  <script>
    //stacked chart
    document.addEventListener("DOMContentLoaded", function() {
      var ctx = document.getElementById('attendance-stacked-bar-chart').getContext('2d');
      var stackedBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($stacked_bar_data['labels']); ?>,
          datasets: <?php echo json_encode($stacked_bar_data['datasets']); ?>
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
   
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  var label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  if (context.parsed.y !== null) {
                    label += context.parsed.y;
                  }
                  return label;
                }
              }
            }
          },
          scales: {
            x: {
              stacked: true
            },
            y: {
              stacked: true
            }
          }
        }
      });

      const schoolYearFilter = document.getElementById('stackedChartFilter');
      const selectedYearSpan = document.getElementById('selectedYear');

      schoolYearFilter.addEventListener('change', function() {
        const selectedYear = this.value;
        selectedYearSpan.textContent = selectedYear || 'N/A'; // Update the title with the selected year
        if (selectedYear) {
          location.href = `dashboard.php?school_year=${selectedYear}`;
        } else {
          location.href = 'dashboard.php'; // Redirect to default page if no year is selected
        }
      });
    });

    //end of stacked chart


    document.addEventListener('DOMContentLoaded', (event) => {
      const table = document.getElementById('lateTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        row.classList.add('clickable-row');
        row.addEventListener('click', function() {
          const studentName = row.getAttribute('data-name');
          window.location.href = `student-details.php?srcode=${encodeURIComponent(studentName)}`;
        });
      }
    });
    document.addEventListener('DOMContentLoaded', (event) => {
      const table = document.getElementById('absencesTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        row.classList.add('clickable-row');
        row.addEventListener('click', function() {
          const studentName = row.getAttribute('data-name');
          window.location.href = `student-details.php?srcode=${encodeURIComponent(studentName)}`;
        });
      }
    });
    document.addEventListener('DOMContentLoaded', (event) => {
      const table = document.getElementById('perfectAttendanceTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let row of rows) {
        row.classList.add('clickable-row');
        row.addEventListener('click', function() {
          const studentName = row.getAttribute('data-name');
          window.location.href = `student-details.php?srcode=${encodeURIComponent(studentName)}`;
        });
      }
    });
  </script>
  <script>
    // pie chart attendance distribution
    document.addEventListener("DOMContentLoaded", function () {
  const presentToday = <?= $present_today ?>;
  const lateToday = <?= $late_today ?>;
  const absentToday = <?= $absent_today ?>;

  // Initialize the pie chart
  const chart = new ApexCharts(document.querySelector("#attendanceSummaryChart"), {
    series: [presentToday, lateToday, absentToday],
    chart: {
      type: 'pie',
      height: 350,
    },
    labels: ['Present', 'Late', 'Absent'],
    colors: ['#4CAF50', '#FFC107', '#F44336'], // Colors for the segments
    legend: {
      position: 'right',
    },
    dataLabels: {
      enabled: true,
    },
  });

  chart.render();

  // Initialize Flatpickr
  const calendar = flatpickr("#calendarFilter", {
    dateFormat: "Y-m-d",
    onChange: function (selectedDates, dateStr) {
      const grade = $('#gradeFilter').val();
      const filter = $('#timeFilter').val();

      // Redirect with the selected date, grade, and filter
      window.location.href = `?filter=${filter}&grade=${grade}&date=${dateStr}`;
    },
  });

  // Handle grade and time filter changes
  $('#gradeFilter, #timeFilter').change(function () {
    const grade = $('#gradeFilter').val();
    const filter = $('#timeFilter').val();
    const date = $('#calendarFilter').val(); // Include the calendar date if set

    // Redirect with the selected grade, filter, and optionally the date
    window.location.href = `?filter=${filter}&grade=${grade}&date=${date || ''}`;
  });
});
    
  </script>
  <script>
    //pie chart gender distribution
      document.addEventListener("DOMContentLoaded", () => {
      var options = {
        series: [<?php echo $male_count; ?>, <?php echo $female_count; ?>],
        chart: {
          type: 'pie',
          height: 350
        },
        labels: ['Male', 'Female'],
        colors: ['#008FFB', '#FF4560'],
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      };
      
      var chart = new ApexCharts(document.querySelector("#genderDistributionChart"), options);
      chart.render();
    });
    // Handle filter changes
    $('#gradeFilter, #timeFilter').change(function() {
        const grade = $('#gradeFilter').val();
        const filter = $('#timeFilter').val();

        window.location.href = `?filter=${filter}&grade=${grade}`;
      });
  </script>
  
  <script>
  // Bar chart attendance overview with updated filter handling
  let byGradeChart;

  $(document).ready(function () {
    // Initialize Flatpickr
    const calendar = flatpickr("#calendarFilter", {
      dateFormat: "Y-m-d",
      onChange: function (selectedDates, dateStr) {
        // Update the card title
        $('#selectedDate').text(' | ' + dateStr);

        // Fetch attendance data for the selected date
        fetchAttendanceData(dateStr, $('#schoolYearFilter').val());
      },
    });

    // Trigger calendar display on icon click
    $('.filter .calendar-icon').on('click', function (e) {
      e.preventDefault(); // Prevent default anchor or button behavior
      $('#calendarFilter').toggle().focus(); // Show and focus the input
    });

    // Handle school year filter change
    $('#schoolYearFilter').on('change', function () {
      const selectedYear = $(this).val();
      if (selectedYear) {
        location.href = `dashboard.php?school_year=${selectedYear}`;
      } else {
        location.href = 'dashboard.php'; // Redirect to default if no year selected
      }
    });

    // Fetch initial data for today's date
    const today = new Date().toISOString().split('T')[0];
    fetchAttendanceData(today, $('#schoolYearFilter').val());
  });

  function fetchAttendanceData(date, schoolYear) {
    $.ajax({
      method: "POST",
      data: {
        date: date,
        schoolYear: schoolYear,
      },
      success: function (data) {
        const attendanceData = JSON.parse(data);
        renderChart(attendanceData);
      },
      error: function (err) {
        console.error(err);
      },
    });
  }

  function renderChart(data) {
    if (byGradeChart) {
      byGradeChart.destroy(); // Destroy the previous chart if it exists
    }

    byGradeChart = new ApexCharts(document.querySelector("#byGradeChart"), {
      series: data.datasets.map((dataset) => ({
        name: dataset.label,
        data: dataset.data,
      })),
      chart: {
        type: "bar",
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: "70%",
          endingShape: "rounded",
        },
      },
      dataLabels: {
        enabled: false,
      },
      stroke: {
        show: true,
        width: 2,
        colors: ["transparent"],
      },
      xaxis: {
        categories: data.labels,
      },
      yaxis: {
        title: {
          text: "Attendance Count",
        },
        beginAtZero: true,
      },
      fill: {
        opacity: 1,
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val;
          },
        },
      },
    });

    byGradeChart.render();
  }
</script>

  <script>
    //line chart monthly attendance
    document.addEventListener("DOMContentLoaded", function() {
      function updateChart(month) {
        // Fetch data for the selected month from PHP variables
        var data = {
          days: <?= json_encode($attendance_overview['days']); ?>,
          presentCounts: <?= json_encode($attendance_overview['presentCounts']); ?>,
          absentCounts: <?= json_encode($attendance_overview['absentCounts']); ?>,
          lateCounts: <?= json_encode($attendance_overview['lateCounts']); ?>
        };

        var lineChartOptions = {
          series: [{
            name: 'Present',
            data: data.presentCounts
          }, {
            name: 'Absent',
            data: data.absentCounts
          },
          {
            name: 'Late',
            data: data.lateCounts
          }],
          chart: {
            height: 280,
            type: 'line'
          },
          stroke: {
            width: [2, 2],
            curve: 'smooth'
          },
          xaxis: {
            categories: data.days
          },
          yaxis: {
            title: {
              text: 'Number of Students'
            }
          },
          colors: ['#FF1654', '#247BA0']
        };

        var attendanceLineChart = new ApexCharts(document.querySelector("#attendance-line-chart"), lineChartOptions);
        attendanceLineChart.render();
      }

      // Initial chart render
      updateChart("<?= $selectedMonth ?>");

      // Handle month filter change
      document.getElementById('monthFilter').addEventListener('change', function() {
        const selectedMonth = this.value;
        if (selectedMonth) {
          location.href = `dashboard.php?month=${selectedMonth}`;
        } else {
          location.href = 'dashboard.php'; // Redirect to default page if no month is selected
        }
      });
    });
  </script>
</body>

</html>
