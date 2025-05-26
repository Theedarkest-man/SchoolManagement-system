<?php
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];

// Fetch student information
$studentQuery = "SELECT student_id, first_name, last_name, email, mobile_no FROM students WHERE student_id = $student_id";
$studentResult = mysqli_query($conn, $studentQuery);
$student = mysqli_fetch_assoc($studentResult);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        #content {
            width: 80%;
            max-width: 1000px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        form {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .form-section, .payment-method-section {
            width: 48%;
        }
        .form-section label,
        .payment-method-section label {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        .form-section input[type="text"],
        .form-section input[type="email"],
        .form-section input[type="number"],
        .payment-method-section input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 0.9em;
        }
        .form-section input[readonly] {
            background-color: #f0f0f0;
        }
        /* Payment method icons */
        .payment-method-icons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .payment-method-icons img {
            width: 40px;
            height: 25px;
            object-fit: contain;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 5px;
            cursor: pointer;
        }
        /* Expiration date section */
        .expiration-date {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .expiration-date select {
            width: 48%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 0.9em;
        }
        /* Button */
        .btn {
            background-color: #7b6ef6;
            color: white;
            padding: 12px;
            width: 100%;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #6d5feb;
        }
        /* Total Amount Display */
        .total-amount {
            margin-top: 40px;
            font-size: 1.1em;
            color: #333;
            font-weight: bold;
            text-align: center;
        }
        .total-amount h3 {
            font-size: 1.2em;
            margin: 10px 0 0;
            color: #7b6ef6;
        }
    </style>
</head>
<body>

<section id="content">
    <h2>Payment</h2>
    <form action="process_payment.php" method="POST">
        <!-- Left Form Section -->
        <div class="form-section">
            <label>Enter Student Name</label>
            <input type="text" name="student_name" value="<?php echo $student['first_name'] . ' ' . $student['last_name']; ?>" readonly>

            <label>Enter Email</label>
            <input type="email" name="email" value="<?php echo $student['email']; ?>" readonly>

            <label>Enter Contact No.</label>
            <input type="text" name="contact_no" value="<?php echo $student['mobile_no']; ?>" readonly>

            <label>Enter Student Number</label>
            <input type="text" name="student_id" value="<?php echo $student['student_id']; ?>" readonly>

            <label>Enter Price</label>
            <input type="number" name="amount" id="amount" required placeholder="12000" oninput="updateTotalAmount()">
        </div>

        <!-- Right Payment Method Section -->
        <div class="payment-method-section">
            <div class="payment-method-icons">
                <img src="images/paypal.png" alt="PayPal">
                <img src="images/visa.png" alt="Visa">
                <img src="images/mastercard.png" alt="Mastercard">
                <img src="images/discover.png" alt="Discover">
                <img src="images/amex.png" alt="Amex">
            </div>
            
            <label>Bank</label>
            <input type="text" name="bank" required placeholder="Bank Name">

            <label>Card Number</label>
            <input type="text" name="card_number" required placeholder="**** **** **** 3947">

            <label>Expiration Date</label>
            <div class="expiration-date">
                <select name="exp_month" required>
                    <option>12</option>
                    <!-- Additional months -->
                </select>
                <select name="exp_year" required>
                    <option>2024</option>
                    <!-- Additional years -->
                </select>
            </div>

            <label>CVV</label>
            <input type="text" name="cvv" required placeholder="123">

            <button type="submit" class="btn">Pay Now</button>
            <!-- Total Amount Display -->
            <div class="total-amount">
                <p>Total Amount</p>
                <h3 id="total-amount-display">R 0</h3>
            </div>
        </div>
    </form>
</section>

<script>
    function updateTotalAmount() {
        const amountInput = document.getElementById('amount');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        const amountValue = amountInput.value || '0';
        totalAmountDisplay.textContent = 'R ' + amountValue;
    }
</script>

</body>
</html>
