<?php
$callbackContent = file_get_contents("php://input");
$callbackData = json_decode($callbackContent, true);

$resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
$resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];

if ($resultCode == 0) {
    // Transaction was successful
    $amount = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
    $mpesaReceiptNumber = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
    $transactionDate = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
    $mobileNumber = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

    // Connect to the database
    $mysqli = new mysqli("localhost", "cashoutc_raccoon", "@Raccoon254", "cashoutc_data");
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Insert the data into the database
    $query = "INSERT INTO data (contact, transaction) VALUES ('$mobileNumber', '$mpesaReceiptNumber')";
    if ($mysqli->query($query) === TRUE) {
        file_put_contents("success_connections.txt", "Record inserted successfully ", FILE_APPEND);
    } else {
		file_put_contents("failed_connections.txt", "Mysql Error: " .  $mysqli->error. "\n" . "Error inserting record: ", FILE_APPEND);
    }

    $mysqli->close();
} else {
    // Transaction was unsuccessful
    file_put_contents("failed_transactions.txt", "Result Code: " . $resultCode . "\n" . "Result Description: " . $resultDesc . "\n", FILE_APPEND);
}
?>
