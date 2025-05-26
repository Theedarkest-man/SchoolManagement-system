<?php
session_start();
include 'db.php';

// Check if the user is logged in and if the course_id is provided
if (!isset($_SESSION['student_id']) || !isset($_GET['course_id'])) {
    header('Location: login.php');
    exit();
}

// Get lecturer details based on course ID
$course_id = $_GET['course_id'];
$query = "
    SELECT l.lecturer_name, l.email, l.contact_no, l.department 
    FROM lecturers l
    JOIN courses c ON c.lecturer_name = l.lecturer_name
    WHERE c.course_id = $course_id
";
$result = mysqli_query($conn, $query);
$lecturer = mysqli_fetch_assoc($result);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $course['course_name']; ?> - Course Details</title>
    <link rel="stylesheet" href="style.css">
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
        <h1>Contact Lecturer</h1>
        <?php if ($lecturer) { ?>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($lecturer['lecturer_name']); ?></p>
            <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($lecturer['email']); ?>">
                <?php echo htmlspecialchars($lecturer['email']); ?></a></p>
            <p><strong>Contact No:</strong> <?php echo htmlspecialchars($lecturer['contact_no']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($lecturer['department']); ?></p>
        <?php } else { ?>
            <p>No lecturer information found for this course.</p>
        <?php } ?>
    </section>
</body>
</html>


