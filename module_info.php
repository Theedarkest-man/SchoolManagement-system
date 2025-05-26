<?php
include 'db.php';

$course_id = $_GET['course_id'];

// Fetch course details, including module overview, introduction, and prescribed textbook
$courseQuery = "SELECT course_name, course_code, module_overview, introduction, prescribed_textbook FROM courses WHERE course_id = $course_id";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);

if (!$course) {
    die("Course not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $course['course_name']; ?> - Course Details</title>
    <link rel="stylesheet" href="module.css">
</head>
<body>

<!-- Sidebar -->
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
        <li><a href="submissions.php?course_id=<?= $course_id ?>">Submission Links</a></li>
        <li><a href="contact_lecturer.php?course_id=<?= $course_id ?>">Contact Lecturer</a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
    <div class="footer-links">
        <a href="#">Privacy</a> | <a href="#">Terms</a> | <a href="#">Accessibility</a>
    </div>
</section>

<!-- Content Section -->
<section id="content">
    <h1>Module Information</h1>
    <div class="module-details">
        <h2>Module Overview</h2>
        <p><?php echo $course['module_overview'] ? $course['module_overview'] : 'Not available'; ?></p>
        
        <h2>Introduction</h2>
        <p><?php echo $course['introduction'] ? $course['introduction'] : 'Not available'; ?></p>
        
        <h2>Prescribed Textbook</h2>
        <p><?php echo $course['prescribed_textbook'] ? $course['prescribed_textbook'] : 'Not available'; ?></p>
    </div>
</section>

</body>
</html>
