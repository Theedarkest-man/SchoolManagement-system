<?php 
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];

// Fetch student information along with program name
$sql = "SELECT students.*, programs.program_name 
        FROM students 
        JOIN programs ON students.program_id = programs.program_id 
        WHERE student_id = $student_id";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $image = $_FILES['profile_image']['name'];
    $target_dir = "profile_image/";
    $target_file = $target_dir . basename($image);

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        // Update profile image path in the database
        $update_query = "UPDATE students SET profile_image = '$image' WHERE student_id = $student_id";
        $conn->query($update_query);
        header("Location: settings.php");
        exit();
    } else {
        echo "Error uploading file.";
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
    <title>Student Profile Settings</title>
    <style>
        /* Wrapper to center and adjust position */
        .center-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            left: 5%; /* Adjust this to move left or right */
        }

        /* General Container */
        .profile-container {
            max-width: 1100px; /* Increased width */
            padding: 40px; /* Increased padding */
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        
        /* Header and Profile Picture */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        
        .profile-header img {
            width: 150px; /* Increased size */
            height: 150px; /* Increased size */
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        
        .profile-header .profile-info {
            flex-grow: 1;
        }
        
        .profile-header h3 {
            margin: 0;
            font-size: 2em; /* Increased font size */
            font-weight: bold;
            color: #333;
        }
        
        .profile-header p {
            margin: 5px 0;
            color: #777;
        }
        
        /* Upload and Delete Button Styling */
        .buttons {
            display: flex;
            gap: 15px;
        }
        
        .buttons label, .buttons button {
            padding: 12px 25px; /* Larger button size */
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
        }
        
        .upload-btn {
            background-color: #007bff;
            color: white;
            border: none;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        /* Details Section */
        .profile-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px; /* Increased gap */
            margin-top: 20px;
        }
        
        .profile-details label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .profile-details input {
            width: 100%;
            padding: 15px; /* Larger input padding */
            border-radius: 5px;
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
            font-size: 1em; /* Larger font size */
        }
        
        /* Single Column Row */
        .profile-details .full-width {
            grid-column: span 2;
        }
        
        /* Icon Inside Input */
        .input-icon {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        
        .input-icon i {
            color: #777;
        }
        
        .input-icon input {
            border: none;
            outline: none;
            background-color: transparent;
            width: 100%;
            font-size: 1em;
        }
        
    </style>
</head>
<body>

<!-- SIDEBAR -->
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-graduation'></i>
        <span class="text">Student Portal</span>
    </a>
    <ul class="side-menu top">
        <li><a href="index.php"><i class='bx bx-home'></i><span class="text">Home</span></a></li>
        <li><a href="course.php"><i class='bx bx-book'></i><span class="text">Course</span></a></li>
        <li><a href="calendar_home.php"><i class='bx bx-calendar-alt'></i><span class="text">Calendar</span></a></li>
        <li><a href="fees.php"><i class='bx bx-money'></i><span class="text">Fees</span></a></li>
        <li><a href="marks.php"><i class='bx bxs-edit'></i><span class="text">Marks</span></a></li>
        <li><a href="exams.php"><i class='bx bx-pencil'></i><span class="text">Exams</span></a></li>
        <li><a href="tasks.php"><i class='bx bx-task'></i><span class="text">Tasks</span></a></li>
    </ul>
    <ul class="side-menu">
        <li class="active"><a href="settings.php"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
        <li><a href="#help"><i class='bx bx-help-circle'></i><span class="text">Help</span></a></li>
        <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
    </ul>
</section>

<div class="center-wrapper">
    <div class="profile-container">
        <h2>Student Profile</h2>
        <div class="profile-header">
            <img src="profile_image/<?php echo $student['profile_image'] ?: 'default.png'; ?>" alt="Profile Picture">
            <div class="profile-info">
                <h3><?php echo $student['first_name'] . " " . $student['last_name']; ?></h3>
                <p><?php echo $student['program_name']; ?></p>
                <p>Rosebank College</p>
            </div>
            <form action="settings.php" method="post" enctype="multipart/form-data" class="buttons">
                <label class="upload-btn" for="profile_image">Upload New Photo</label>
                <input type="file" id="profile_image" name="profile_image" style="display: none;" onchange="this.form.submit();">
                <button type="submit" class="delete-btn" name="delete_photo">Delete</button>
            </form>
        </div>

        <div class="profile-details">
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" value="<?php echo $student['first_name']; ?>" disabled>
            </div>

            <div>
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" value="<?php echo $student['last_name']; ?>" disabled>
            </div>

            <div class="full-width">
                <label for="student_number">Student Number</label>
                <input type="text" id="student_number" value="<?php echo $student['id_number']; ?>" disabled>
            </div>

            <div class="full-width">
                <label for="email">Email Address</label>
                <div class="input-icon">
                    <i class='bx bx-envelope'></i>
                    <input type="email" id="email" value="<?php echo $student['email']; ?>" disabled>
                </div>
            </div>

            <div class="full-width">
                <label for="phone">Phone Number</label>
                <div class="input-icon">
                    <i class='bx bx-phone'></i>
                    <input type="text" id="phone" value="<?php echo $student['mobile_no']; ?>" disabled>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
