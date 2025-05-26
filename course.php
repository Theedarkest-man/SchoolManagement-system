<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

// Fetch student information
$student_id = $_SESSION['student_id'];
$sql = "SELECT * FROM students WHERE student_id = $student_id";
$student_result = $conn->query($sql);
$student = $student_result->fetch_assoc();

// Fetch announcements from the announcements table
$announcementQuery = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
$announcementResult = mysqli_query($conn, $announcementQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Enrolled Courses</title>
    <style>
        #content {
            flex-grow: 1;
            padding: 40px;
        }
        .page-title {
            font-size: 1.5em;
            font-weight: 600;
            color: #0a2c50;
            margin-bottom: 20px;
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        .course-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .course-card h2 {
            font-size: 1.2em;
            color: #0a2c50;
            margin-bottom: 8px;
        }
        .course-card p {
            font-size: 0.9em;
            color: #666;
            margin: 4px 0;
        }
        .course-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #0a2c50;
            color: #ffffff;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
        }
        .course-card a:hover {
            background-color: #ff5a5f;
        }
        .announcements {
            background: #ffffff;
            padding: 15px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .announcement {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .announcement:last-child {
            border-bottom: none;
        }
        .announcement p {
            font-size: 0.9em;
            margin: 5px 0;
        }
        .announcement p strong {
            color: #0a2c50;
        }
        .toggle-btn {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 15px;
            background-color: #0a2c50;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }
        .toggle-btn:hover {
            background-color: #ff5a5f;
        }
        .hidden {
            display: none;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadCourses(semester) {
            $.ajax({
                url: "load_courses.php",
                type: "POST",
                data: {
                    program_id: <?php echo $student['program_id']; ?>,
                    semester: semester
                },
                success: function(data) {
                    $("#courses-" + semester).html(data);
                }
            });
        }

        $(document).ready(function() {
            loadCourses(1);
            loadCourses(2);
        });
    </script>
</head>
<body>

<!-- Sidebar -->
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-graduation'></i>
        <span class="text">Student Dashboard</span>
    </a>
    <ul class="side-menu top">
        <li><a href="index.php"><i class='bx bx-home'></i><span class="text">Home</span></a></li>
        <li class="active"><a href="course.php"><i class='bx bx-book'></i><span class="text">Courses</span></a></li>
        <li><a href="calendar_home.php"><i class='bx bx-calendar-alt'></i><span class="text">Calendar</span></a></li>
        <li><a href="fees.php"><i class='bx bx-money'></i><span class="text">Fees</span></a></li>
        <li><a href="marks.php"><i class='bx bxs-edit'></i><span class="text">Marks</span></a></li>
        <li><a href="exams.php"><i class='bx bx-pencil'></i><span class="text">Exams</span></a></li>
        <li><a href="tasks.php"><i class='bx bx-task'></i><span class="text">Tasks</span></a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="settings.php"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
        <li><a href="#help"><i class='bx bx-help-circle'></i><span class="text">Help</span></a></li>
        <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Sign Out</span></a></li>
    </ul>
</section>

<!-- Content -->
<section id="content">
    <h2 class="page-title">Enrolled Courses</h2>

    <!-- Semester 1 Courses -->
    <h3>Semester 1 Modules</h3>
    <div id="courses-1" class="course-grid"></div>

    <!-- Toggle Button for Semester 2 Courses -->
    <div class="toggle-btn" onclick="$('#courses-2').toggleClass('hidden');">
        Toggle Semester 2 Modules
    </div>

    <!-- Semester 2 Courses (Initially Hidden) -->
    <div id="courses-2" class="course-grid hidden"></div>

    <!-- Announcements Section -->
    <h3>Announcements:</h3>
    <div class="announcements">
        <?php while ($announcement = mysqli_fetch_assoc($announcementResult)) { ?>
            <div class="announcement">
                <p><strong><?php echo date("Y/m/d - h:i A", strtotime($announcement['created_at'])); ?></strong></p>
                <p><strong><?php echo htmlspecialchars($announcement['title']); ?>:</strong> <?php echo htmlspecialchars($announcement['content']); ?></p>
                <p>Kind regards,<br>Admin</p>
            </div>
        <?php } ?>
    </div>
</section>

</body>
</html>
