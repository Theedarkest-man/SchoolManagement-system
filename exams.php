<?php
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];

$query = "SELECT course_name, course_code, exam_date, exam_time, venue, status FROM exams WHERE published = 1 ORDER BY exam_date";
$result = mysqli_query($conn, $query);

$announcementQuery = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
$announcementResult = mysqli_query($conn, $announcementQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exams</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
    /* Main Content Styling */
    #content {
        flex-grow: 1;
        padding: 30px;
        background-color: #f4f6f9;
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Make content fill the viewport height */
    }

    /* Header and Page Titles */
    h2 {
        font-size: 2em;
        font-weight: 700;
        color: #0a2c50;
        margin-bottom: 15px;
    }

    h3 {
        font-size: 1.5em;
        color: #0a2c50;
        margin-top: 25px;
    }

    /* Exams Table Container */
    .exams-container {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: white;
        margin-top: 15px;
        margin-bottom: 20px;
    }

    /* Exams Table */
    .exams-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
    }

    .exams-table th, .exams-table td {
        padding: 12px 20px;
        font-size: 1em;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    .exams-table th {
        background-color: #f5f5f5;
        color: #333;
        font-weight: bold;
        text-align: center;
    }

    /* Status Cell Styling */
    .status-cell {
        text-align: center;
        font-weight: bold;
        color: white;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .status-cell.upcoming {
        background-color: #27ae60;
    }
    .status-cell.completed {
        background-color: #3498db;
    }
    .status-cell.cancelled {
        background-color: #e74c3c;
    }

    /* Announcements Section */
    .announcements {
        background-color: white;
        padding: 15px;
        border-radius: 8px;
        margin-top: auto; /* Push announcements to the bottom */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .announcements .announcement {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .announcements .announcement:last-child {
        border-bottom: none;
    }

    .announcements .announcement p {
        margin: 5px 0;
        color: #333;
    }

    .announcements .announcement strong {
        color: #0a2c50;
    }

    /* Responsive Design */
    @media screen and (max-width: 768px) {
        .exams-table th, .exams-table td {
            font-size: 0.9em;
            padding: 10px;
        }

        h2 {
            font-size: 1.6em;
        }

        h3 {
            font-size: 1.3em;
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
        <li><a href="marks.php"><i class='bx bxs-edit'></i><span class="text">Marks</span></a></li>
        <li class="active"><a href="exams.php"><i class='bx bx-pencil'></i><span class="text">Exams</span></a></li>
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

<!-- Content Section -->
<section id="content">
    <h2>Exam Board</h2>
    <h3>Coming Exams</h3>
    <div class="exams-container">
        <table class="exams-table">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($exam = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($exam['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($exam['course_code']); ?></td>
                        <td><?php echo date("M d, Y", strtotime($exam['exam_date'])); ?></td>
                        <td><?php echo date("h:i A", strtotime($exam['exam_time'])); ?></td>
                        <td><?php echo htmlspecialchars($exam['venue']); ?></td>
                        <td class="status-cell <?php echo strtolower($exam['status']); ?>">
                            <?php echo ucfirst($exam['status']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Announcements Section -->
    <h3>Announcements:</h3>
    <div class="announcements">
        <?php while ($announcement = mysqli_fetch_assoc($announcementResult)) { ?>
            <div class="announcement">
                <p><strong><?php echo date("Y/m/d - h:i A", strtotime($announcement['created_at'])); ?></strong></p>
                <p><strong><?php echo htmlspecialchars($announcement['title']); ?>:</strong> <?php echo htmlspecialchars($announcement['content']); ?></p>
                <p>Kind regards,<br>Admin</p>
            </div>
        <?php } ?>
    </div>
</section>

</body>
</html>
