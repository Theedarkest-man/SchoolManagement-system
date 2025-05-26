<?php
session_start();
include 'db.php';

$student_id = $_SESSION['student_id'];
$amount = $_POST['amount'];

// Save payment to database
$insertPaymentQuery = "INSERT INTO fees (student_id, fee_amount, paid, due_date) VALUES ($student_id, $amount, 1, NOW())";
mysqli_query($conn, $insertPaymentQuery);

// Redirect to success page
header("Location: payment_success.php");
exit();
?>
