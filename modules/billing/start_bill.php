<?php

session_start();

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



$last = $pdo->query(
    "SELECT invoice_no 
     FROM invoices 
     ORDER BY invoice_id DESC 
     LIMIT 1"
)->fetch(PDO::FETCH_ASSOC);



if($last){

    $number = intval(
        str_replace("INV","",$last['invoice_no'])
    );

    $number++;

}else{

    $number = 1;

}



$invoice_no = "INV" . str_pad($number,4,"0",STR_PAD_LEFT);



// save invoice record

$stmt = $pdo->prepare(
"INSERT INTO invoices
(
invoice_no,
cashier_id,
status
)
VALUES
(
:invoice_no,
:cashier_id,
:status
)"
);


$stmt->execute([

    ":invoice_no"=>$invoice_no,

    ":cashier_id"=>1,

    ":status"=>"pending"

]);



// save session

$_SESSION['invoice_no'] = $invoice_no;



echo $invoice_no;


?>