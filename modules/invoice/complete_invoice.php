<?php

$page = "invoices";

require_once '../../includes/auth.php';
require_once '../../config/database.php';

$database = new Database();
$pdo = $database->connect();

if(!isset($_GET['invoice_no'])){
    die("Invoice Not Found");
}

$invoice_no = $_GET['invoice_no'];

// Check remaining products
$stmt = $pdo->prepare("
SELECT COUNT(*) AS total
FROM invoice_items
WHERE invoice_no = :invoice_no
AND remaining_quantity > 0
");

$stmt->execute([
    ":invoice_no" => $invoice_no
]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row['total'] > 0){

    echo "<script>
            alert('There are still remaining products!');
            window.location='view.php?invoice_no=$invoice_no';
          </script>";
    exit();

}

// Complete Invoice
$stmt = $pdo->prepare("
UPDATE invoices
SET status='completed'
WHERE invoice_no=:invoice_no
");

$stmt->execute([
    ":invoice_no"=>$invoice_no
]);

echo "<script>
        alert('Invoice Completed Successfully!');
        window.location='view.php?invoice_no=$invoice_no';
      </script>";