<?php
session_start(); // Start the session
include 'db.php'; // Include your database connection

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']); // Trim to avoid extra spaces
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        $errorMessage = "Both email and password are required.";
    } else {
        // Check if the email exists in the students table
        $query = "SELECT * FROM students WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        // If the email exists, check the password
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // Store the student_id in session and redirect
                $_SESSION['student_id'] = $user['student_id'];
                header("Location: index.php");
                exit();
            } else {
                $errorMessage = "Incorrect password. Please try again.";
            }
        } else {
            $errorMessage = "Invalid email or password!";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rosebank College - Student Login</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        /* Splash screen styling */
        .splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #cc0000; /* Red background */
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 10;
            opacity: 1;
            transition: opacity 1s ease;
            visibility: visible;
            overflow: hidden;
        }

        .splash-screen h1 {
            font-size: 2.5em;
            color: white; /* White text for the welcome message */
            margin-bottom: 20px;
            text-align: center;
            transform: translateY(-100px); /* Start off-screen */
            animation: drop 2s ease forwards; /* Animate to drop */
        }

        /* Waterfall effect */
        .waterfall {
            position: absolute;
            top: 0;
            left: 50%;
            width: 2px;
            height: 100%;
            background: white;
            opacity: 0;
            animation: fall 4s linear infinite;
        }

        @keyframes fall {
            0% {
                top: -100%;
                opacity: 0;
            }
            100% {
                top: 100%;
                opacity: 1;
            }
        }

        /* Loading spinner styling */
        .loading-indicator {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3); /* Lighter border */
            border-top: 5px solid white; /* White spinner */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Split animation styling */
        .split-left, .split-right {
            position: fixed;
            top: 0;
            width: 50%;
            height: 100%;
            background-color: #cc0000;
            z-index: 9;
            transition: transform 1s ease;
        }

        .split-left {
            left: 0;
            transform: translateX(0);
        }

        .split-right {
            right: 0;
            transform: translateX(0);
        }

        /* Main container styling */
        .login-container {
            display: none;
            flex-direction: column;
            width: 90%;
            max-width: 800px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 1s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Login form styling */
        .login-form {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #f5f6fa;
        }

        .login-form h2 {
            text-align: center;
            font-size: 1.8em;
            color: #cc0000;
            margin-bottom: 30px;
        }

        .login-form .form-group {
            margin-bottom: 20px;
        }

        .login-form label {
            font-size: 0.9em;
            color: #cc0000;
            display: block;
            margin-bottom: 8px;
        }

        .login-form input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .login-form button {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: #cc0000;
            color: white;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-form button:hover {
            background-color: #a80000;
        }

        /* Links and error message styling */
        .extra-links {
            text-align: center;
            margin-top: 20px;
        }

        .extra-links a {
            color: #cc0000;
            text-decoration: none;
            font-size: 0.9em;
        }

        .extra-links a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            display: <?php echo isset($error_message) ? 'block' : 'none'; ?>; /* Display error message if set */
        }
        .register-button {
    display: block;
    width: 100%;
    padding: 12px;
    text-align: center;
    margin-top: 15px;
    background-color: #ccc;
    color: #cc0000;
    font-size: 1em;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.register-button:hover {
    background-color: #a80000;
    color: white;
}


.error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            display: <?php echo !empty($errorMessage) ? 'block' : 'none'; ?>; /* Display error message if set */
        }


        /* Drop animation for welcome text */
        @keyframes drop {
            0% {
                transform: translateY(-100px); /* Start from above */
                opacity: 0; /* Start invisible */
            }
            100% {
                transform: translateY(0); /* Drop to normal position */
                opacity: 1; /* Fade in */
            }
        }
    </style>
</head>
<body>

    <!-- Splash Screen -->
    <div class="splash-screen">
        <h1>Welcome to Rosebank College</h1>
        <div class="loading-indicator"></div>
        <div class="waterfall"></div> <!-- Waterfall effect inside splash screen -->
    </div>

    <!-- Main Login Container -->
    <div class="login-container">
        <div class="login-form">
            <h2>Student Login</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>

                <!-- Register button -->
                <a href="register.php" class="register-button">Register</a>

                <!-- Display error message if login fails -->
                <?php if (!empty($errorMessage)): ?>
                    <div class="error-message"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>


    <!-- Split animations -->
    <div class="split-left"></div>
    <div class="split-right"></div>

    <script>
        // Hide splash screen and show login form after animation
        setTimeout(function() {
            document.querySelector('.splash-screen').style.opacity = 0;
            document.querySelector('.splash-screen').style.visibility = 'hidden';
            document.querySelector('.login-container').style.display = 'flex';
            document.querySelector('.split-left').style.transform = 'translateX(-100%)';
            document.querySelector('.split-right').style.transform = 'translateX(100%)';
        }, 6000); // Splash screen visible for 6 seconds
    </script>

</body>
</html>