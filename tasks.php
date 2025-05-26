<?php
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];

// Handle task submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_task'])) {
    $task_id = $_POST['task_id'];
    $submitted_file = $_FILES['submitted_file']['name'];
    $target_dir = __DIR__ . "/uploads/submissions/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($submitted_file);

    if (move_uploaded_file($_FILES["submitted_file"]["tmp_name"], $target_file)) {
        $submitQuery = "UPDATE task_assignments SET status = 'Submitted', submitted_file = '$submitted_file' 
                        WHERE task_id = '$task_id' AND student_id = '$student_id'";
        mysqli_query($conn, $submitQuery);
    } else {
        echo "Error uploading file.";
    }
}

// Fetch the tasks assigned to this student from task_assignments
$taskQuery = "
    SELECT tasks.task_id, tasks.task_name, tasks.description, tasks.file, task_assignments.status, 
           task_assignments.mark, courses.course_name 
    FROM task_assignments 
    JOIN tasks ON task_assignments.task_id = tasks.task_id
    JOIN courses ON tasks.course_id = courses.course_id 
    WHERE task_assignments.student_id = '$student_id'
";
$taskResult = mysqli_query($conn, $taskQuery);

// Initialize task counts
$totalTasks = 0;
$pendingTasks = 0;
$submittedTasks = 0;

// Calculate task status counts
while ($task = mysqli_fetch_assoc($taskResult)) {
    $totalTasks++;
    if ($task['status'] == 'Pending') {
        $pendingTasks++;
    } elseif ($task['status'] == 'Submitted') {
        $submittedTasks++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Task Management</title>

    <style>
        /* General Table Styles */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            text-align: left;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .styled-table thead tr {
            background-color: #0a2c50;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }

        .styled-table th, .styled-table td {
            padding: 12px 15px;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        /* Alternate row colors */
        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        /* Status Styles */
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
            text-align: center;
        }

        .status.pending {
            background-color: #dc3545;
            color: #ffffff;
        }

        .status.submitted {
            background-color: #28a745;
            color: #ffffff;
        }

        /* Download Link Styles */
        .styled-table td a {
            color: #0a2c50;
            text-decoration: none;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .styled-table td a:hover {
            background-color: #e69522;
            color: white;
        }

        #content {
            flex-grow: 1;
            padding: 40px;
        }

        .page-title {
            font-size: 2em;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0a2c50;
        }

        /* Task Stats Styling */
        .task-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-graduation'></i>
        <span class="text">Student Dashboard</span>
    </a>
    <ul class="side-menu top">
        <li><a href="index.php"><i class='bx bx-home'></i><span class="text">Home</span></a></li>
        <li><a href="course.php"><i class='bx bx-book'></i><span class="text">Course</span></a></li>
        <li><a href="calendar_home.php"><i class='bx bx-calendar-alt'></i><span class="text">Calendar</span></a></li>
        <li><a href="fees.php"><i class='bx bx-money'></i><span class="text">Fees</span></a></li>
        <li><a href="marks.php"><i class='bx bxs-edit'></i><span class="text">Marks</span></a></li>
        <li><a href="exams.php"><i class='bx bx-pencil'></i><span class="text">Exams</span></a></li>
        <li class="active"><a href="tasks.php"><i class='bx bx-task'></i><span class="text">Tasks</span></a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="settings.php"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
        <li><a href="#help"><i class='bx bx-help-circle'></i><span class="text">Help</span></a></li>
        <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
    </ul>
</section>

<!-- CONTENT -->
<section id="content">
    <h2 class="page-title">Tasks</h2>

    <!-- Task Stats -->
    <div class="task-stats">
        <div>Total Tasks: <?php echo $totalTasks; ?></div>
        <div>Pending: <?php echo $pendingTasks; ?></div>
        <div>Submitted: <?php echo $submittedTasks; ?></div>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Description</th>
                <th>Course</th>
                <th>Status</th>
                <th>Marks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($taskResult, 0);
            while ($task = mysqli_fetch_assoc($taskResult)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                    <td><?php echo htmlspecialchars($task['course_name']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($task['status']); ?>">
                            <?php echo htmlspecialchars($task['status']); ?>
                        </span>
                    </td>
                    <td><?php echo isset($task['mark']) ? htmlspecialchars($task['mark']) : 'Not Graded'; ?></td>
                    <td>
                        <?php if (!empty($task['file']) && file_exists('uploads/' . htmlspecialchars($task['file']))): ?>
                            <a href="uploads/<?php echo htmlspecialchars($task['file']); ?>" download>Download Task</a>
                        <?php else: ?>
                            <span>No File Available</span>
                        <?php endif; ?>
                        
                        <?php if ($task['status'] == 'Pending'): ?>
                            <form action="tasks.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                <input type="file" name="submitted_file" required>
                                <button type="submit" name="submit_task">Submit Task</button>
                            </form>
                        <?php elseif ($task['status'] == 'Submitted'): ?>
                            <span class="status submitted">Submitted</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
</body>
</html>
