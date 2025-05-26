<?php
session_start();
include 'db.php';

$course_id = $_GET['course_id'];

// Fetch course details including course_code
$courseQuery = "SELECT course_name, course_code, lecturer_name, class_date, class_time, venue_room FROM courses WHERE course_id = $course_id";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $course['course_name']; ?> - Course Details</title>
    <link rel="stylesheet" href="module.css">
</head>
<body>
<section id="course-sidebar">
    <div class="course-header">
        <?php echo $course['course_name']; ?><br>
        <span><?php echo $course['course_code']; ?></span>
    </div>
    <ul class="side-menu">
        <li><a href="module_home.php?course_id=<?= $course_id ?>">Module Home Page</a></li>
        
        <li class="section-title">Getting Started:</li>
        <li><a href="module_info.php?course_id=<?= $course_id ?>">Module Information</a></li>
        <li><a href="calendar.php?course_id=<?= $course_id ?>">Calendar</a></li>
        
        <li class="section-title">Module Content:</li>
        <li><a href="learning_units.php?course_id=<?= $course_id ?>">Learning Units</a></li>
        <li><a href="resources.php?course_id=<?= $course_id ?>">More Resources</a></li>

        <li class="section-title">Assessment Submissions:</li>
<div class="submission-links-container">
    <li><a href="submissions.php?course_id=<?= $course_id ?>">Submission Links</a></li>
</div>
<li><a href="contact_lecturer.php?course_id=<?= $course_id ?>">Contact Lecturer</a></li>

    </ul>
    <ul class="side-menu">
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
    <div class="footer-links">
        <a href="#">Privacy</a> | <a href="#">Terms</a> | <a href="#">Accessibility</a>
    </div>
</section>

<section id="content">
    <h1><?php echo $course['course_name']; ?></h1>
    <p>Lecturer: <?php echo $course['lecturer_name']; ?></p>
    <p>Class Date: <?php echo $course['class_date']; ?></p>
    <p>Class Time: <?php echo $course['class_time']; ?></p>
    <p>Venue: <?php echo $course['venue_room']; ?></p>
</section>
</body>
</html>
