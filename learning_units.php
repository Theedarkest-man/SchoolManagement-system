<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

// Validate and get the course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Fetch course details
$courseQuery = "SELECT course_name, course_code FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($courseQuery);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$courseResult = $stmt->get_result();
$course = $courseResult->fetch_assoc();

// Check if the course exists
if (!$course) {
    echo "Course not found.";
    exit;
}

// Fetch learning units for the course
$query = "SELECT * FROM learning_units WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$units_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['course_name']); ?> - Learning Units</title>
    <link rel="stylesheet" href="module.css">
    <style>
    /* Modal styling */
    .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 50px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .modal img {
    width: 100%;
    height: 200%;
    object-fit: fill; 
}
        .modal-content {
            margin: auto;
            padding: 20px;
            background-color: white;
            width: 80%;
            height: 80vh;
            max-width: 800px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-btn:hover,
        .close-btn:focus {
            color: black;
        }
        iframe, img {
            width: 100%;
            height: 100%;
            border: none;
        }
        .file-viewer {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
#fileContainer {
    width: 100%;
    height: 95%; 
    padding: 10px;
    overflow: auto; 
}
    </style>
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
    <h2>Learning Units</h2>
    <div class="learning-units-container">
        <?php while ($unit = $units_result->fetch_assoc()) { ?>
            <div class="learning-unit">
                <h3><?php echo htmlspecialchars($unit['title']); ?></h3>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($unit['description']); ?></p>
                <p><strong>Activities:</strong> <?php echo htmlspecialchars($unit['activity_count']); ?></p>
                <p><strong>Max Time:</strong> <?php echo htmlspecialchars($unit['max_time']); ?></p>
                <?php if (!empty($unit['file_path'])) { ?>
                    <button onclick="openModal('<?php echo htmlspecialchars($unit['file_path']); ?>')" class="open-file-button">Open File</button>
                <?php } else { ?>
                    <p><em>No file available for this unit.</em></p>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>

<!-- Modal structure -->
<div id="fileModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div id="fileContainer"></div>
    </div>
</div>

<script>
    function openModal(filePath) {
        const fileContainer = document.getElementById("fileContainer");
        const fileModal = document.getElementById("fileModal");
        
        // Get file extension
        const fileExtension = filePath.split('.').pop().toLowerCase();
        
        // Handle file types
        let content = '';
        if (fileExtension === 'pdf') {
            content = `<iframe src="${filePath}" type="application/pdf"></iframe>`;
        } else if (fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png' || fileExtension === 'gif') {
            content = `<img src="${filePath}" alt="Image">`;
        } else if (fileExtension === 'txt') {
            content = `<iframe src="${filePath}" type="text/plain"></iframe>`;
        } else if (fileExtension === 'docx' || fileExtension === 'doc') {
            // Use Google Viewer for DOC/DOCX files
            content = `<iframe src="https://docs.google.com/gview?url=http://localhost/SchoolManagementSystem/${filePath}&embedded=true"></iframe>`;
        } else {
            content = `<p>Unsupported file type. <a href="${filePath}" target="_blank">Download File</a> to view.</p>`;
        }
        
        fileContainer.innerHTML = content;
        fileModal.style.display = "block";
    }

    function closeModal() {
        document.getElementById("fileModal").style.display = "none";
        document.getElementById("fileContainer").innerHTML = ''; // Clear the container
    }
</script>
</body>
</html>
