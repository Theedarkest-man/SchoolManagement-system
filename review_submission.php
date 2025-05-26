<?php
session_start();
include 'db.php';

if (!isset($_GET['submission_id'])) {
    header('Location: submissions.php');
    exit();
}

$submission_id = intval($_GET['submission_id']);
$student_id = $_SESSION['student_id'];

// Fetch submission and course details
$submissionQuery = "SELECT s.*, c.course_name, c.course_code 
                    FROM submissions s 
                    JOIN courses c ON s.course_id = c.course_id 
                    WHERE s.submission_id = $submission_id 
                    AND s.student_id = $student_id";
$submissionResult = mysqli_query($conn, $submissionQuery);
$submission = mysqli_fetch_assoc($submissionResult);

if (!$submission) {
    echo "No submission found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Submission: <?php echo htmlspecialchars($submission['submission_text']); ?></title>
    <link rel="stylesheet" href="module.css">
    <style>
        #content {
            padding: 20px;
            margin-left: 250px;
            display: flex;
        }
        .file-viewer {
            flex: 1;
            height: 500px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .details-panel {
            width: 300px;
            margin-left: 20px;
            padding: 15px;
            background-color: #e8e8e8;
            border-radius: 10px;
        }
        .details-panel h2 {
            font-size: 1.2em;
            margin-bottom: 15px;
        }
        .details-panel .mark {
            font-size: 1.1em;
            margin-top: 10px;
        }
        .details-panel .submission-link {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<section id="course-sidebar">
    <div class="course-header">
        <?php echo htmlspecialchars($submission['course_name']); ?><br>
        <span><?php echo htmlspecialchars($submission['course_code']); ?></span>
    </div>
    <ul class="side-menu">
        <li><a href="module_home.php?course_id=<?= $submission['course_id'] ?>">Module Home Page</a></li>
        <li class="section-title">Getting Started:</li>
        <li><a href="module_info.php?course_id=<?= $submission['course_id'] ?>">Module Information</a></li>
        <li><a href="calendar.php?course_id=<?= $submission['course_id'] ?>">Calendar</a></li>
        <li class="section-title">Module Content:</li>
        <li><a href="learning_units.php?course_id=<?= $submission['course_id'] ?>">Learning Units</a></li>
        <li><a href="resources.php?course_id=<?= $submission['course_id'] ?>">More Resources</a></li>
        <li class="section-title">Assessment Submissions:</li>
        <li><a href="submissions.php?course_id=<?= $submission['course_id'] ?>">Submission Links</a></li>
        <li><a href="contact_lecturer.php?course_id=<?= $submission['course_id'] ?>">Contact Lecturer</a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
    <div class="footer-links">
        <a href="#">Privacy</a> | <a href="#">Terms</a> | <a href="#">Accessibility</a>
    </div>
</section>

<section id="content">
    <div class="file-viewer">
        <?php
        $fileUrl = htmlspecialchars($submission['submission_file']);
        $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
        
        if ($fileExtension === 'pdf') {
            echo "<iframe src='$fileUrl' width='100%' height='100%'></iframe>";
        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
            echo "<iframe src='https://docs.google.com/gview?url=" . urlencode($fileUrl) . "&embedded=true' width='100%' height='100%'></iframe>";
        } else {
            echo "<p>Download the file to view: <a href='$fileUrl' download>Download File</a></p>";
        }
        ?>
    </div>
    <div class="details-panel">
        <h2>Assignment Details</h2>
        <p><strong>Mark:</strong> <?= isset($submission['total_marks']) ? $submission['total_marks'] : '--' ?>/100</p>
        <div class="submission-link">
            <a href="<?= $fileUrl ?>" download>Download File</a>
        </div>
    </div>
</section>
</body>
</html>
