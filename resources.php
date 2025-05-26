<?php
session_start();
include 'db.php';

// Check if the user is logged in and if the course_id is provided
if (!isset($_SESSION['student_id']) || !isset($_GET['course_id'])) {
    header('Location: login.php');
    exit();
}

$course_id = $_GET['course_id'];

// Fetch course details
$courseQuery = "SELECT course_name, course_code FROM courses WHERE course_id = $course_id";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);

// Fetch resources for the course
$query = "SELECT * FROM resources WHERE course_id = $course_id";
$result = mysqli_query($conn, $query);
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
    <h1>Additional Resources</h1>
    <ul>
        <?php while ($resource = mysqli_fetch_assoc($result)) { ?>
            <li>
                <strong><?php echo htmlspecialchars($resource['resource_title']); ?></strong><br>
                <a href="<?php echo htmlspecialchars($resource['resource_link']); ?>" target="_blank">
                    <?php echo htmlspecialchars($resource['resource_link']); ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</section>
</body>
</html>
