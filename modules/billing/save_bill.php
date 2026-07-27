<?php

session_start();

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



if(!isset($_SESSION['invoice_no'])){

    header("Location:start_bill.php");
    exit();

}


$invoice_no = $_SESSION['invoice_no'];


// Get Bill Total

$stmt = $pdo->prepare("
SELECT SUM(total) AS total
FROM temp_cart
WHERE invoice_no = :invoice_no
");


$stmt->execute([
":invoice_no"=>$invoice_no
]);


$data = $stmt->fetch(PDO::FETCH_ASSOC);


$total = $data['total'] ?? 0;



// Update Invoice

$stmt = $pdo->prepare("
UPDATE invoices

SET 
total_amount = :total,
status='completed'

WHERE invoice_no = :invoice_no
");


$stmt->execute([

":total"=>$total,

":invoice_no"=>$invoice_no

]);



// Add Income

$stmt = $pdo->prepare("
INSERT INTO transactions

(
transaction_date,
type,
category,
description,
amount
)

VALUES

(
CURDATE(),
'IN',
'Sales',
'Invoice Sale',
:amount
)

");


$stmt->execute([

":amount"=>$total

]);

echo "
<script>
window.location.href='index.php';
</script>
";
exit();

?>