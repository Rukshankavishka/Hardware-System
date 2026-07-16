<?php

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


echo $invoice_no;

?>