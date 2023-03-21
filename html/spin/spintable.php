<?php
// connect to the database
require_once '../../db_connect.php';
session_start();

// get the latest spin data for the current user
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT * FROM spin WHERE email=:email ORDER BY date DESC");
$stmt->bindParam(':email', $email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// return the data as JSON
header('Content-Type: application/json');
echo json_encode($result);
?>
