<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection
include 'db.php';

// Fetch student information along with program name
$student_id = $_SESSION['student_id'];
$sql = "SELECT s.*, p.program_name 
        FROM students s 
        JOIN programs p ON s.program_id = p.program_id 
        WHERE s.student_id = $student_id";
$student_result = $conn->query($sql);
$student = $student_result->fetch_assoc();

// Fetch last read notification time from database
$last_checked = $student['last_read_notifications'];

// If no last_read_notifications time, set it to the current time initially
if (empty($last_checked)) {
    $last_checked = date('Y-m-d H:i:s');
    $update_last_read_sql = "UPDATE students SET last_read_notifications = '$last_checked' WHERE student_id = $student_id";
    $conn->query($update_last_read_sql);
}

// Fetch latest announcements for the timeline
$announcements_sql = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5";
$announcements_result = $conn->query($announcements_sql);

// Fetch latest exams for the timeline
$exams_sql = "SELECT * FROM exams ORDER BY exam_date DESC LIMIT 5";
$exams_result = $conn->query($exams_sql);

// Fetch latest marks for the timeline
$marks_sql = "SELECT * FROM marks WHERE student_id = $student_id ORDER BY date_assessed DESC LIMIT 5";
$marks_result = $conn->query($marks_sql);

// Fetch unread notifications
$unread_announcements_sql = "SELECT * FROM announcements WHERE created_at > '$last_checked'";
$unread_announcements = $conn->query($unread_announcements_sql);
$unread_announcement_count = $unread_announcements->num_rows;

$unread_exams_sql = "SELECT * FROM exams WHERE exam_date > '$last_checked'";
$unread_exams = $conn->query($unread_exams_sql);
$unread_exam_count = $unread_exams->num_rows;

$unread_marks_sql = "SELECT * FROM marks WHERE student_id = $student_id AND date_assessed > '$last_checked'";
$unread_marks = $conn->query($unread_marks_sql);
$unread_marks_count = $unread_marks->num_rows;

// Total unread notifications
$total_unread = $unread_announcement_count + $unread_exam_count + $unread_marks_count;

$image_path = "profile_image/";
$default_image = $image_path . "default.png"; // Default image path
// Path to profile images
$image_path = "profile_image/";
$profile_image = !empty($student['profile_image']) && file_exists($image_path . $student['profile_image']) 
                ? $image_path . $student['profile_image'] 
                : $image_path . "default.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="style.css">
	<title>Student Portal</title>

	<style>
		/* Flex container to align banner and card side by side */
.banner-card-container {
	display: flex;
	justify-content: flex-start; /* Align items to the left */
	align-items: center;
	width: 100%; /* Ensure full-width container */
	margin: 20px auto;
	padding: 0;
	box-sizing: border-box;
	position: relative;
}

/* Banner styling */
.banner {
	width: 75%; /* Adjust width as needed */
	position: relative;
	background-image: url('img/students.jpg');
	background-size: cover;
	background-position: center;
	height: 250px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
	margin-left: 10px; /* Space from the left side */
}

.banner-overlay {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(13, 46, 77, 0.6);
	border-radius: 12px;
	z-index: 1;
}

.banner-content {
	position: relative;
	z-index: 2;
	text-align: center;
}

.banner-text {
	font-size: 1.5em;
	font-weight: bold;
}

.banner-button {
	background-color: #DB504A;
	color: white;
	padding: 10px 20px;
	border-radius: 5px;
	border: none;
	margin-top: 20px;
	cursor: pointer;
}

.banner-button:hover {
	background-color: #e69522;
}

/* Card styling */
.card {
            position: absolute;
            width: 18%;
            background-color: #0a2c50;
            color: white;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            left: 81%;
        }

        .card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .card i {
            font-size: 3em;
            color: #ffffff;
        }

        .card h1 {
            margin-top: 10px;
            font-size: 1.5em;
        }

        .card .title {
            font-size: 1.2em;
            color: white;
        }

        .card .University {
            color: #FFD700;
            font-size: 1em;
        }


		.timeline {
			width: 80%;
			position: relative;
			padding: 20px;
			border-left: 2px solid #007bff;
			margin-left: 20px;
			max-height: 300px; 
			overflow-y: auto; 
		}

		.timeline-item {
			margin-bottom: 20px;
			padding-left: 20px;
			position: relative;
		}

		.timeline-item::before {
			content: '';
			position: absolute;
			left: -10px;
			top: 0;
			width: 20px;
			height: 20px;
			background: #007bff;
			border-radius: 50%;
		}

		.timeline-title {
			font-size: 1.2em;
			font-weight: bold;
		}

		.timeline-time {
			color: #999;
			font-size: 0.9em;
		}

		.timeline-content {
			margin-top: 10px;
			padding: 10px;
			background-color: #f9f9f9;
			border-radius: 5px;
			box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
		}

		/* Popup styling */
		.notification-popup {
			position: absolute;
			right: 20px;
			top: 60px;
			width: 300px;
			background-color: white;
			box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
			padding: 20px;
			display: none; /* Hidden by default */
			z-index: 100;
			border-radius: 8px;
		}
		
		.notification-popup h3 {
			font-size: 1.2em;
			margin-bottom: 10px;
		}

		.notification-popup ul {
			list-style: none;
			padding: 0;
		}

		.notification-popup li {
			margin-bottom: 10px;
		}

		.notification-popup li p {
			margin: 0;
			font-size: 0.9em;
		}

		/* Styling the notification icon */
		.notification {
			position: relative;
		}

		.notification .num {
			position: absolute;
			top: -10px;
			right: -10px;
			background-color: red;
			color: white;
			border-radius: 50%;
			padding: 2px 6px;
			font-size: 0.9em;
		}
	</style>
</head>
<body>

	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxs-graduation'></i>
			<span class="text">Student Portal</span>
		</a>
		<ul class="side-menu top">
			<li class="active"><a href="index.php">
                <i class='bx bx-home'></i>
                <span class="text">Home</span>
            </a></li>

            <li><a href="course.php">
                <i class='bx bx-book'></i>
                <span class="text">Courses</span>
            </a></li>

            <li><a href="calendar_home.php">
                <i class='bx bx-calendar-alt'></i>
                <span class="text">Calendar</span>
            </a></li>

            <li><a href="fees.php">
                <i class='bx bx-money'></i>
                <span class="text">Fees</span>
            </a></li>

            <li><a href="marks.php">
                <i class='bx bxs-edit'></i>
                <span class="text">Marks</span>
            </a></li>

            <li><a href="exams.php">
                <i class='bx bx-pencil'></i>
                <span class="text">Exams</span>
            </a></li>
			<li><a href="tasks.php"><i class='bx bx-task'></i><span class="text">Tasks</span></a></li>

		</ul>
		<ul class="side-menu">
			<li><a href="settings.php">
				<i class='bx bxs-cog'></i>
				<span class="text">Settings</span>
			</a></li>
			<li><a href="#help">
                <i class='bx bx-help-circle'></i>
                <span class="text">Help</span>
            </a></li>
			<li><a href="logout.php" class="logout">
				<i class='bx bxs-log-out-circle'></i>
				<span class="text">Sign Out</span>
			</a></li>
		</ul>
	</section>
	<!-- SIDEBAR -->

	<!-- CONTENT -->
	<section id="content">
		<nav>
			<i class='bx bx-menu'></i>
			<a href="#" class="nav-link">Categories</a>
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
				</div>
			</form>

			<!-- Notification Icon -->
			<a href="#" class="notification" onclick="toggleNotificationPopup()">
				<i class='bx bxs-bell'></i>
				<?php if ($total_unread > 0): ?>
				<span class="num"><?php echo $total_unread; ?></span>
				<?php endif; ?>
			</a>

			<a href="#" class="profile">
				<i class='bx bxs-user-circle'></i>
			</a>
			<h5 class="info-online">
				<?php echo $student['first_name'] . " " . $student['last_name']; ?> <br>
			</h5>
		</nav>

		<!-- Notification Popup -->
		<div class="notification-popup" id="notificationPopup">
			<h3>Recent Posts</h3>
			<ul>
				<?php while ($announcement = $unread_announcements->fetch_assoc()): ?>
				<li>
					<strong>Announcement:</strong>
					<p><?php echo $announcement['title']; ?></p>
				</li>
				<?php endwhile; ?>
				
				<?php while ($exam = $unread_exams->fetch_assoc()): ?>
				<li>
					<strong>Exam:</strong>
					<p><?php echo $exam['course_name']; ?> on <?php echo $exam['exam_date']; ?></p>
				</li>
				<?php endwhile; ?>

				<?php while ($mark = $unread_marks->fetch_assoc()): ?>
				<li>
					<strong>Mark:</strong>
					<p><?php echo $mark['course_name']; ?> - Marks: <?php echo $mark['marks_obtained']; ?>/<?php echo $mark['total_marks']; ?></p>
				</li>
				<?php endwhile; ?>
			</ul>
		</div>

		<script>
			// Function to toggle the notification popup
			function toggleNotificationPopup() {
				var popup = document.getElementById("notificationPopup");
				if (popup.style.display === "block") {
					popup.style.display = "none";
				} else {
					popup.style.display = "block";
					resetNotificationCount(); // Reset notification count when opened
				}
			}

			// Function to reset the notification count
			function resetNotificationCount() {
				<?php
					// Update the last_read_notifications field in the database
					$update_last_read_sql = "UPDATE students SET last_read_notifications = NOW() WHERE student_id = $student_id";
					$conn->query($update_last_read_sql);
				?>
				document.querySelector('.num').style.display = 'none'; // Hide the notification badge
			}
		</script>


		<!-- Banner and Card Container -->
		<div class="banner-card-container">
			<!-- Banner -->
			<div class="banner">
				<div class="banner-overlay"></div>
				<div class="banner-content">
					<div class="banner-text">Discover your agenda of the week</div>
					<button class="banner-button">View agenda</button>
				</div>
			</div>
<!-- Student Information Card -->
<div class="card">
    <img src="<?php echo $profile_image; ?>" alt="Profile Image">
    <h1><?php echo $student['first_name'] . " " . $student['last_name']; ?></h1>
    <p class="title">Student</p>
    <p class="Program"><?php echo $student['program_name']; ?></p>
    <p class="University">Rosebank College</p>
</div>
		</div>

	
<br>
<br>
		

		<!-- Timeline for Announcements, Exams, and Marks -->
        <div class="timeline">
            <!-- Display announcements -->
            <?php while($announcement = $announcements_result->fetch_assoc()): ?>
            <div class="timeline-item">
                <div class="timeline-title">New Announcement</div>
                <div class="timeline-time"><?php echo $announcement['created_at']; ?></div>
                <div class="timeline-content">
                    <h4><?php echo $announcement['title']; ?></h4>
                    <p><?php echo $announcement['content']; ?></p>
                </div>
            </div>
            <?php endwhile; ?>

            <!-- Display exams -->
            <?php while($exam = $exams_result->fetch_assoc()): ?>
            <div class="timeline-item">
                <div class="timeline-title">Upcoming Exam</div>
                <div class="timeline-time"><?php echo $exam['exam_date']; ?> at <?php echo $exam['exam_time']; ?></div>
                <div class="timeline-content">
                    <h4><?php echo $exam['course_name']; ?> (<?php echo $exam['course_code']; ?>)</h4>
                    <p>Venue: <?php echo $exam['venue']; ?> <br> Description: <?php echo $exam['description']; ?></p>
                </div>
            </div>
            <?php endwhile; ?>

            <!-- Display marks -->
            <?php while($mark = $marks_result->fetch_assoc()): ?>
            <div class="timeline-item">
                <div class="timeline-title">New Marks Published</div>
                <div class="timeline-time"><?php echo $mark['date_assessed']; ?></div>
                <div class="timeline-content">
                    <h4><?php echo $mark['course_name']; ?></h4>
                    <p>Marks Obtained: <?php echo $mark['marks_obtained']; ?>/<?php echo $mark['total_marks']; ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
	</section>
</body>
</html>
