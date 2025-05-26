<?php
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];

$semestersQuery = "SELECT DISTINCT semester FROM courses ORDER BY semester";
$semestersResult = mysqli_query($conn, $semestersQuery);
$semesters = mysqli_fetch_all($semestersResult, MYSQLI_ASSOC);

$selectedSemester = isset($_GET['semester']) ? $_GET['semester'] : 1;
$marksQuery = "
    SELECT c.course_name, c.course_code, sub.total_marks 
    FROM submissions sub
    JOIN courses c ON sub.course_id = c.course_id 
    WHERE sub.student_id = $student_id AND c.semester = $selectedSemester AND sub.total_marks IS NOT NULL";

$marksResult = mysqli_query($conn, $marksQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marks</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
       
       /* Marks Page Specific Styling */

/* Main Content */
#content {
    flex-grow: 1;
    padding: 30px;
    background-color: #f4f6f9;
}

/* Header and Page Title */
h2 {
    font-size: 2em;
    font-weight: 700;
    color: #0a2c50;
    margin-bottom: 15px;
}

/* Semester Selector */
.semester-selector {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.semester-selector label {
    font-size: 1.1em;
    color: #0a2c50;
    margin-right: 10px;
    font-weight: bold;
}

.semester-selector select {
    padding: 8px 12px;
    font-size: 1em;
    border-radius: 5px;
    border: 1px solid #0a2c50;
    background-color: #ffffff;
    color: #0a2c50;
    font-weight: bold;
    outline: none;
}

/* Marks Container */
.marks-container {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background-color: white;
}

/* Marks Header */
.marks-header {
    background-color: #0a2c50;
    color: white;
    padding: 15px;
    text-align: center;
    font-size: 1.3em;
    font-weight: bold;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

/* Marks Table */
.marks-table {
    width: 100%;
    border-collapse: collapse;
}

.marks-table th, .marks-table td {
    padding: 15px;
    font-size: 1em;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.marks-table th {
    background-color: #f5f5f5;
    color: #333;
    font-weight: bold;
    text-align: center;
}

.marks-table td {
    color: #333;
    font-weight: 500;
}

.marks-cell {
    text-align: right;
    font-weight: bold;
    color: #e74c3c;
}

/* Hover Effect on Table Rows */
.marks-table tr:hover {
    background-color: #f0f4f7;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .marks-table th, .marks-table td {
        font-size: 0.9em;
        padding: 10px;
    }

    .semester-selector label,
    .semester-selector select {
        font-size: 0.9em;
    }

    h2 {
        font-size: 1.6em;
    }
}

    </style>
</head>
<body>

<!-- Sidebar -->
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-graduation'></i>
        <span class="text">Student Portal</span>
    </a>
    <ul class="side-menu top">
        <li><a href="index.php"><i class='bx bx-home'></i><span class="text">Home</span></a></li>
        <li><a href="course.php"><i class='bx bx-book'></i><span class="text">Courses</span></a></li>
        <li><a href="calendar_home.php"><i class='bx bx-calendar-alt'></i><span class="text">Calendar</span></a></li>
        <li><a href="fees.php"><i class='bx bx-money'></i><span class="text">Fees</span></a></li>
        <li class="active"><a href="marks.php"><i class='bx bxs-edit'></i><span class="text">Marks</span></a></li>
        <li><a href="exams.php"><i class='bx bx-pencil'></i><span class="text">Exams</span></a></li>
        <li><a href="tasks.php"><i class='bx bx-task'></i><span class="text">Tasks</span></a></li>

    </ul>

    <ul class="side-menu">
        <li><a href="#settings"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
        <li><a href="#help"><i class='bx bx-help-circle'></i><span class="text">Help</span></a></li>
        <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Sign Out</span></a></li>
    </ul>
</section>

<section id="content">
    <h2>Marks</h2>
    <div class="semester-selector">
        <label for="semester">Select Semester:</label>
        <select id="semester" onchange="changeSemester()">
            <?php foreach ($semesters as $semester) { ?>
                <option value="<?php echo $semester['semester']; ?>" <?php if ($selectedSemester == $semester['semester']) echo 'selected'; ?>>
                    Semester <?php echo $semester['semester']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="marks-container">
        <div class="marks-header">Marks for Semester <?php echo $selectedSemester; ?></div>
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($marksResult) > 0) {
                    while ($row = mysqli_fetch_assoc($marksResult)) { ?>
                        <tr>
                            <td><strong><?php echo $row['course_name'] . " - " . $row['course_code']; ?></strong></td>
                            <td class="marks-cell">
                                <?php echo $row['total_marks'] ? $row['total_marks'] . '/100' : 'N/A'; ?>
                            </td>
                        </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">No marks available for this semester.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    function changeSemester() {
        const semester = document.getElementById("semester").value;
        window.location.href = "marks.php?semester=" + semester;
    }
</script>

</body>
</html>
