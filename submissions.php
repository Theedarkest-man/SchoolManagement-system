<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id']) || !isset($_GET['course_id'])) {
    header('Location: login.php');
    exit();
}

$course_id = intval($_GET['course_id']);
$student_id = $_SESSION['student_id'];

// Fetch course details
$courseQuery = "SELECT course_name, course_code FROM courses WHERE course_id = $course_id";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);

// Fetch existing submissions for the course
$submissionQuery = "SELECT * FROM submissions WHERE course_id = $course_id AND student_id = $student_id";
$submissionResult = mysqli_query($conn, $submissionQuery);

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['submission_file'])) {
    $submission_text = mysqli_real_escape_string($conn, $_POST['submission_text']);
    $targetDir = "uploads/";
    $fileName = basename($_FILES["submission_file"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'txt');

    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["submission_file"]["tmp_name"], $targetFilePath)) {
            $insertQuery = "INSERT INTO submissions (student_id, course_id, submission_text, submission_file) 
                            VALUES ($student_id, $course_id, '$submission_text', '$targetFilePath')";
            if (mysqli_query($conn, $insertQuery)) {
                $submission_id = mysqli_insert_id($conn);
                header("Location: review_submission.php?submission_id=" . $submission_id);
                exit();
            } else {
                $errorMessage = "Error: " . mysqli_error($conn);
            }
        } else {
            $errorMessage = "Failed to upload file.";
        }
    } else {
        $errorMessage = "Only PDF, DOC, DOCX, JPG, PNG, and TXT files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['course_name']); ?> - Submissions</title>
    <link rel="stylesheet" href="module.css">

    <style>
        #content {
            padding: 20px;
            margin-left: 250px;
        }
        .submission-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .submission-list {
            max-width: 600px;
            margin: 20px auto;
        }
        .submission-list ul {
            list-style-type: none;
            padding: 0;
        }
        .submission-list li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .submission-list a {
            color: #007bff;
            text-decoration: none;
        }
        .submission-list a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<section id="course-sidebar">
    <div class="course-header">
        <?php echo htmlspecialchars($course['course_name']); ?><br>
        <span><?php echo htmlspecialchars($course['course_code']); ?></span>
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

<section id="content">
    <h1>Submission Links</h1>
    <?php if (!empty($errorMessage)) { echo "<p class='error'>$errorMessage</p>"; } ?>

    <div class="submission-form">
        <h2>Submit Your Assignment</h2>
        <form action="submissions.php?course_id=<?= $course_id ?>" method="post" enctype="multipart/form-data">
            <label for="submission_text">Submission Text (optional):</label>
            <textarea name="submission_text" id="submission_text" rows="4"></textarea>

            <label for="submission_file">Upload File:</label>
            <input type="file" name="submission_file" required>

            <button type="submit">Submit</button>
        </form>
    </div>

    <div class="submission-list">
        <h2>Your Submissions</h2>
        <ul>
            <?php while ($submission = mysqli_fetch_assoc($submissionResult)) { ?>
                <li>
                    <a href="review_submission.php?submission_id=<?= $submission['submission_id'] ?>">
                        <?php echo htmlspecialchars($submission['submission_text']); ?>
                    </a><br>
                    Submission Date: <?php echo htmlspecialchars($submission['submission_date']); ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</section>
</body>
</html>
