<?php
include 'db.php'; // Ensure this connects correctly to your phpMyAdmin database

$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobileNo = trim($_POST['mobile_no']);
    $qualification = trim($_POST['qualification']);
    $intakeYear = trim($_POST['intake_year']);
    $race = trim($_POST['race']);
    $idNumber = trim($_POST['id_number']);
    $address = trim($_POST['address']);
    $programId = $_POST['program'];
    $password = $_POST['password'];

    // Validate fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($mobileNo) || empty($qualification) ||
        empty($intakeYear) || empty($race) || empty($idNumber) || empty($address) || empty($password)) {
        $errorMessage = "All fields are required. Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } else {
        // Check if email already exists
        $emailQuery = "SELECT * FROM students WHERE email='$email'";
        $emailResult = mysqli_query($conn, $emailQuery);

        if (mysqli_num_rows($emailResult) > 0) {
            $errorMessage = "This email is already registered!";
        } else {
            // Insert into the database
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $query = "INSERT INTO students (first_name, last_name, email, mobile_no, qualification, intake_year, race, id_number, address, program_id, password) 
                      VALUES ('$firstName', '$lastName', '$email', '$mobileNo', '$qualification', '$intakeYear', '$race', '$idNumber', '$address', '$programId', '$passwordHash')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                $successMessage = "Registration successful! Redirecting to login page...";
                header("refresh:3;url=login.php");
            } else {
                $errorMessage = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow-y: auto;
        }

        /* Splash screen with spinner */
        .splash-screen {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            border: 8px solid #ff0000;
            border-top: 8px solid #ffffff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1.5s linear infinite;
            position: absolute;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Split screen effect */
        .splash-left, .splash-right {
            width: 50%;
            height: 100%;
        }

        .splash-left {
            background-color: red;
            animation: slideOutLeft 1.5s forwards;
        }

        .splash-right {
            background-color: rgb(0, 0, 0);
            animation: slideOutRight 1.5s forwards;
        }

        @keyframes slideOutLeft {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        @keyframes slideOutRight {
            0% { transform: translateX(0); }
            100% { transform: translateX(100%); }
        }

        /* Registration form */
        .registration-container {
            display: none;
            width: 50%;
            max-width: 600px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            z-index: 1;
            animation: fadeInForm 1s forwards;
        }

        @keyframes fadeInForm {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .registration-header {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .registration-form h2 {
            text-align: center;
            font-size: 1.4em;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .error-message, .success-message {
            display: none;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1em;
        }

        .error-message {
            color: #e74c3c;
        }

        .success-message {
            color: #2ecc71;
        }

        .registration-form .form-group {
            margin-bottom: 12px;
        }

        .registration-form label {
            font-size: 0.85em;
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }

        .registration-form input, .registration-form select, .registration-form textarea {
            width: 100%;
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.85em;
        }

        .registration-form button {
            width: 100%;
            padding: 8px;
            border: none;
            background-color: #e74c3c;
            color: white;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .registration-form button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<!-- Splash Screen with Spinner -->
<div class="splash-screen" id="splash-screen">
    <div class="spinner"></div>
    <div class="splash-left"></div>
    <div class="splash-right"></div>
</div>

<!-- Registration Form -->
<div class="registration-container" id="registration-container">
    <div class="registration-header">Registration</div>
    <div class="registration-form">
        <h2>Register as a Student</h2>
        <div class="error-message" id="error-message">Error: Something went wrong.</div>
        <div class="success-message" id="success-message">Registration successful!</div>
        <form id="registration-form" method="post" action="register.php">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" id="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="mobile_no">Mobile No:</label>
                <input type="text" name="mobile_no" id="mobile_no" required>
            </div>
            <div class="form-group">
                <label for="qualification">Qualification:</label>
                <input type="text" name="qualification" id="qualification" required>
            </div>
            <div class="form-group">
                <label for="intake_year">Intake Year:</label>
                <input type="number" name="intake_year" id="intake_year" required>
            </div>
            <div class="form-group">
                <label for="race">Race:</label>
                <input type="text" name="race" id="race" required>
            </div>
            <div class="form-group">
                <label for="id_number">ID Number/Passport:</label>
                <input type="text" name="id_number" id="id_number" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea name="address" id="address" required></textarea>
            </div>
            <div class="form-group">
                <label>Select Program:</label>
                <select name="program" required>
                    <?php
                    $programQuery = "SELECT * FROM programs";
                    $programResult = mysqli_query($conn, $programQuery);
                    while ($program = mysqli_fetch_assoc($programResult)) {
                        echo "<option value='".$program['program_id']."'>".$program['program_name']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Register now</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.onload = function() {
        // Start by showing the splash screen with spinner for 3 seconds
        setTimeout(function() {
            // After 3 seconds, trigger the split screen effect and fade-in form
            document.getElementById("splash-screen").style.display = "none";
            document.getElementById("registration-container").style.display = "block";

            // Start split screen animation
            document.querySelector('.splash-left').style.animation = 'slideOutLeft 1.5s forwards';
            document.querySelector('.splash-right').style.animation = 'slideOutRight 1.5s forwards';
        }, 3000);
    };
</script>

</body>
</html>