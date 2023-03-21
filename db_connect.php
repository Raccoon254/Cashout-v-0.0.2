<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cashoutc_data";


/*

    $mysqli = new mysqli("localhost", "cashoutc_raccoon", "@Raccoon254", "cashoutc_data");
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }*/

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
