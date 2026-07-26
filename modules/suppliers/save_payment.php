<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



$purchase_id = $_POST['purchase_id'];
$payment_amount = $_POST['payment_amount'];



/* Current Purchase Details */

$stmt = $pdo->prepare("
SELECT paid_amount, total, supplier_id
FROM purchase_history
WHERE purchase_id = :id
");


$stmt->execute([
    ":id"=>$purchase_id
]);


$purchase = $stmt->fetch(PDO::FETCH_ASSOC);



$new_paid = $purchase['paid_amount'] + $payment_amount;


$new_balance = $purchase['total'] - $new_paid;



/* Update Payment */


$stmt = $pdo->prepare("

UPDATE purchase_history

SET 
paid_amount = :paid,
balance = :balance

WHERE purchase_id = :id

");


$stmt->execute([

":paid"=>$new_paid,

":balance"=>$new_balance,

":id"=>$purchase_id

]);



header(
"Location:supplier_details.php?id=".$purchase['supplier_id']
);

exit();


?>