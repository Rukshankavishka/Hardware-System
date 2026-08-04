<?php

$page = "invoices";

require_once '../../includes/auth.php';
require_once '../../config/database.php';

$database = new Database();
$pdo = $database->connect();

if(!isset($_GET['invoice_no'])){
    die("Invoice not found");
}

$invoice_no = $_GET['invoice_no'];

// Invoice Details
$stmt = $pdo->prepare("
SELECT *
FROM invoices
WHERE invoice_no = :invoice_no
");

$stmt->execute([
":invoice_no"=>$invoice_no
]);

$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$invoice){
    die("Invoice not found");
}

// Invoice Items
$stmt = $pdo->prepare("
SELECT *
FROM invoice_items
WHERE invoice_no = :invoice_no
");

$stmt->execute([
":invoice_no"=>$invoice_no
]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';

?>

<div class="container-fluid mt-4">

<h3>Invoice Details</h3>

<hr>

<h5>Invoice No : <?= $invoice['invoice_no']; ?></h5>

<h5>Status : <?= strtoupper($invoice['status']); ?></h5>

<h5>Total : Rs. <?= number_format($invoice['total_amount'],2); ?></h5>

<h5>Date : <?= $invoice['created_at']; ?></h5>

<hr>

<table class="table table-bordered">

<thead>

<tr>

<th>Product</th>
<th>Qty</th>
<th>Taken</th>
<th>Remaining</th>

</tr>

</thead>

<tbody>

<?php foreach($items as $row){ ?>

<tr>

<td><?= $row['product_name']; ?></td>

<td><?= $row['quantity']; ?></td>

<td><?= $row['taken_quantity']; ?></td>

<td><?= $row['remaining_quantity']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

<?php

if($invoice['status']=="pending"){

?>

<a href="update_item.php?invoice_no=<?= $invoice_no; ?>"
class="btn btn-warning">

Update Products

</a>

<?php } ?>

<?php if($invoice['status']=="pending"){ ?>

<a href="complete_invoice.php?invoice_no=<?= $invoice_no; ?>"
   class="btn btn-success"
   onclick="return confirm('Complete this invoice?');">

    Complete Invoice

</a>

<?php } ?>


<a href="index.php"
class="btn btn-secondary">

Back

</a>

</div>

<?php require_once '../../includes/footer.php'; ?>