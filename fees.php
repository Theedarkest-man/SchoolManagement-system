<?php
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];

// Define total fees
$totalFees = 50000;

// Fetch student details
$studentQuery = "SELECT student_id, first_name, last_name, mobile_no, address FROM students WHERE student_id = $student_id";
$studentResult = mysqli_query($conn, $studentQuery);
$student = mysqli_fetch_assoc($studentResult);

// Fetch fees details and calculate total paid amount
$feesQuery = "SELECT * FROM fees WHERE student_id = $student_id AND paid = 1";
$feesResult = mysqli_query($conn, $feesQuery);

// Calculate the total amount paid
$totalPaid = 0;
while ($fee = mysqli_fetch_assoc($feesResult)) {
    $totalPaid += $fee['fee_amount'];
}

// Calculate remaining balance
$remainingBalance = $totalFees - $totalPaid;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Fees</title>
    <style>
       
        h2, h3 {
            color: #333;
            margin: 10px 0;
        }
        h2.page-title {
            font-size: 1.8em;
            margin-bottom: 20px;
        }


        /* Main Content */
        #content {
            flex: 1;
            padding: 20px;
        }

        /* Account Information */
        .account-info {
            background: #ccc;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .account-info div {
            font-size: 0.9em;
        }
        .account-info p {
            margin: 5px 0;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
        }
        .action-buttons .btn {
            background-color: #6c63ff;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.85em;
        }
        .action-buttons .btn:hover {
            background-color: #5753c9;
        }

        /* Payment History */
        .fees-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        .fees-table th, .fees-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .fees-table th {
            background-color: #f4f4f4;
            color: #333;
        }
        .fees-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Balance Info */
        .balance-info {
            text-align: center;
            background: #f4f4f4;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            width: 180px;
            margin-left: auto;
        }

        /* Footer Note */
        .footer-note {
            background-color: #ccc;
            padding: 15px;
            border-radius: 10px;
            font-size: 0.8em;
            color: #333;
            margin-top: 20px;
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
        <li><a href="course.php"><i class='bx bx-book'></i><span class="text">Courses</span></a></li>
        <li><a href="calendar_home.php"><i class='bx bx-calendar-alt'></i><span class="text">Calendar</span></a></li>
        <li class="active"><a href="fees.php"><i class='bx bx-money'></i><span class="text">Fees</span></a></li>
        <li><a href="marks.php"><i class='bx bxs-edit'></i><span class="text">Marks</span></a></li>
        <li><a href="exams.php"><i class='bx bx-pencil'></i><span class="text">Exams</span></a></li>
        <li><a href="tasks.php"><i class='bx bx-task'></i><span class="text">Tasks</span></a></li>

    </ul>
    <ul class="side-menu">
        <li><a href="settings.php"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
        <li><a href="#help"><i class='bx bx-help-circle'></i><span class="text">Help</span></a></li>
        <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Sign Out</span></a></li>
    </ul>
</section>

<!-- CONTENT -->
<section id="content">
    <h2 class="page-title">Fees</h2>

    <!-- Account Information Section -->
    <div class="account-info">
        <div>
            <p><strong>Account Information</strong></p>
            <p>Student ID: <?php echo $student['student_id']; ?></p>
            <p>Name: <?php echo $student['first_name'] . " " . $student['last_name']; ?></p>
            <p>Contact: <?php echo $student['mobile_no']; ?></p>
        </div>
        <div>
            <p><strong>Address</strong></p>
            <p><?php echo $student['address']; ?></p>
        </div>
    </div>

    <!-- Buttons -->
    <div class="action-buttons">
        <a href="payment.php" class="btn">Make a Payment</a>
        <button onclick="window.print()" class="btn">Print Report</button>
    </div>

    <!-- Payment History Section -->
    <h3>Payment History</h3>
    <table class="fees-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>ID</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($feesResult, 0);
            while ($fee = mysqli_fetch_assoc($feesResult)) { ?>
                <tr>
                    <td><?php echo date("d M Y", strtotime($fee['due_date'])); ?></td>
                    <td>Registration fees</td>
                    <td><?php echo $fee['fee_id']; ?></td>
                    <td>R <?php echo number_format($fee['fee_amount'], 2); ?></td>
                    <td><?php echo $fee['paid'] ? 'Success' : 'Pending'; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Remaining Balance Section -->
    <div class="balance-info">
        Balance<br>R <?php echo number_format($remainingBalance, 2); ?>
    </div>

    <!-- Footer Note -->
    <div class="footer-note">
        <p>Notes:</p>
        <p>- Use Student ID as payment reference.</p>
        <p>- Separate payments for each student if applicable.</p>
    </div>
</section>

</body>
</html>
