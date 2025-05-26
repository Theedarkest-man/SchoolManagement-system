<?php
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];

// Fetch student information
$studentQuery = "SELECT student_id, first_name, last_name, qualification FROM students WHERE student_id = '$student_id'";
$studentResult = mysqli_query($conn, $studentQuery);
$student = mysqli_fetch_assoc($studentResult);

// Fetch announcements from the announcements table
$announcementQuery = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
$announcementResult = mysqli_query($conn, $announcementQuery);

$announcements = [];
while ($row = mysqli_fetch_assoc($announcementResult)) {
    $announcements[] = [
        "title" => $row['title'],
        "content" => $row['content'],
        "date" => date("Y-m-d", strtotime($row['created_at'])), 
        "datetime" => date("Y/m/d - h:i A", strtotime($row['created_at']))
    ];
}

// Fetch exams from the exams table
$examQuery = "SELECT course_name, exam_date, description FROM exams ORDER BY exam_date DESC";
$examResult = mysqli_query($conn, $examQuery);

$exams = [];
while ($row = mysqli_fetch_assoc($examResult)) {
    $exams[] = [
        "title" => $row['course_name'],
        "content" => $row['description'],
        "date" => date("Y-m-d", strtotime($row['exam_date'])),
        "datetime" => date("Y/m/d - h:i A", strtotime($row['exam_date']))
    ];
}

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    
    // Assuming there's a 'courses' table
    $courseQuery = "SELECT course_name, course_code FROM courses WHERE course_id = '$course_id'";
    $courseResult = mysqli_query($conn, $courseQuery);
    
    if ($courseResult && mysqli_num_rows($courseResult) > 0) {
        $course = mysqli_fetch_assoc($courseResult);
    } else {
        $course = null; // Handle the case where no course is found
    }
} else {
    // If course_id is not passed, handle the error appropriately
    $course = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $course['course_name']; ?> - Course Details</title>
    <link rel="stylesheet" href="module.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>

    <style>
        /* Content Styling */
        #content {
            flex-grow: 1;
            padding: 40px;
        }
        .page-title {
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0a2c50;
        }
        #calendar-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .calendar-small {
            width: 100%;
            height: 400px; /* Ensure height is set for visibility */
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
            margin: 5px 0;
            font-size: 0.9em;
        }
        .announcement p strong {
            color: #0a2c50;
        }

        /* Tooltip Styling */
        .fc-tooltip {
            position: fixed;
            z-index: 10001;
            background-color: white;
            color: #000;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: none;
            font-size: 0.9em;
            max-width: 200px;
            word-wrap: break-word;
        }
    </style>
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

<!-- CONTENT -->
<section id="content">
    <h2 class="page-title">Calendar</h2>

    <div id="calendar-container">
        <div id="calendar" class="calendar-small"></div>
    </div>

    <h3>Announcements & Exams:</h3>
    <div class="announcements">
        <?php foreach ($announcements as $announcement): ?>
            <div class="announcement">
                <p><strong><?php echo $announcement['datetime']; ?></strong></p>
                <p><strong><?php echo htmlspecialchars($announcement['title']); ?>:</strong> <?php echo htmlspecialchars($announcement['content']); ?></p>
                <p>Kind regards,<br>Admin</p>
            </div>
        <?php endforeach; ?>

        <?php foreach ($exams as $exam): ?>
            <div class="announcement">
                <p><strong><?php echo $exam['datetime']; ?></strong></p>
                <p><strong>Exam - <?php echo htmlspecialchars($exam['title']); ?>:</strong> <?php echo htmlspecialchars($exam['content']); ?></p>
                <p>Kind regards,<br>Admin</p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tooltip for Calendar Events -->
    <div class="fc-tooltip" id="tooltip"></div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var tooltip = document.getElementById('tooltip');

    // PHP arrays for events encoded as JSON
    var announcements = <?php echo json_encode($announcements); ?>;
    var exams = <?php echo json_encode($exams); ?>;

    // Create events array for FullCalendar
    var events = [];

    // Add announcement events
    announcements.forEach(function(announcement) {
        events.push({
            title: announcement.title,
            start: announcement.date,
            description: announcement.content,
            color: "#FF5733"
        });
    });

    // Add exam events
    exams.forEach(function(exam) {
        events.push({
            title: "Exam - " + exam.title,
            start: exam.date,
            description: exam.content,
            color: "#2E86C1"
        });
    });

    // Initialize FullCalendar with the events
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        events: events,
        eventMouseEnter: function(info) {
            // Show tooltip with the event description
            tooltip.innerHTML = "<strong>" + info.event.title + "</strong><br>" + info.event.extendedProps.description;
            tooltip.style.display = "block";
            tooltip.style.top = (info.jsEvent.pageY + 10) + "px"; // Position slightly below the cursor
            tooltip.style.left = (info.jsEvent.pageX + 10) + "px"; // Position slightly to the right of the cursor
        },
        eventMouseLeave: function(info) {
            // Hide the tooltip when the mouse leaves the event
            tooltip.style.display = "none";
        }
    });

    calendar.render();
});

</script>
</body>
</html>
