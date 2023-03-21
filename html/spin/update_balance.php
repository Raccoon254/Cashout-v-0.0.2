<?php
require_once '../../db_connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php"); // redirect to login page if the user is not logged in
    exit;
}

$email = $_SESSION['email'];
$amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;

if ($amount <= 0) {
    echo 'Error: Invalid amount';
    exit;
}

// Retrieve the user's current balance from the database
$sql = "SELECT balance FROM users WHERE email=:email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$current_balance = $row['balance'];

//Insert data to the spin table
$sql = "INSERT INTO `spin`(`email`, `prize`) VALUES (:email, :amount)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':amount', $amount);

if ($stmt->execute()) {
    //Return nothing
} else {
    echo "Error inserting record: " . $stmt->errorInfo()[2];
}


// Update the user's balance in the database
$new_balance = $current_balance + $amount;
$sql = "UPDATE users SET balance=:new_balance, previous=:current_balance WHERE email=:email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':new_balance', $new_balance);
$stmt->bindParam(':current_balance', $current_balance);
$stmt->bindParam(':email', $email);

if ($stmt->execute()) {
    echo $new_balance; // return the new balance as a response
} else {
    echo 'Error: Failed to update balance';
}
