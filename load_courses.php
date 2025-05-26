<?php
include 'db.php';

$program_id = $_POST['program_id'];
$semester = $_POST['semester'];

$query = "SELECT * FROM courses WHERE program_id = $program_id AND semester = $semester";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($course = mysqli_fetch_assoc($result)) {
        echo "<div class='course-card'>";
        echo "<h2>" . $course['course_name'] . "</h2>";
        echo "<p><strong>Lecturer:</strong> " . $course['lecturer_name'] . "</p>";
        echo "<p><strong>Days:</strong> " . $course['class_days'] . "</p>";
        echo "<p><strong>Time:</strong> " . $course['class_time'] . "</p>";
        echo "<p><strong>Venue:</strong> " . $course['venue_room'] . "</p>";
        echo "<a href='module_home.php?course_id=" . $course['course_id'] . "'>Open module</a>"; // "View Details" button
        echo "</div>";
    }
} else {
    echo "<p>No courses available for this semester.</p>";
}
?>
