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
    // Insert the data into the database
    file_put_contents("success_transactions.txt", "Result Code: " .$mpesaReceiptNumber . "\n" . "Result Description: " . $amount . "\n"."Mobile Number:" .$mobileNumber ."\n", FILE_APPEND);

    $mysqli->close();
} else {
    // Transaction was unsuccessful
    file_put_contents("failed_transactions.txt", "Result Code: " . $resultCode . "\n" . "Result Description: " . $resultDesc . "\n", FILE_APPEND);
}
?>