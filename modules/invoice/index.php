<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

$page = "invoices";

$database = new Database();
$pdo = $database->connect();

$status = $_GET['status'] ?? '';

$sql = "SELECT invoice_no,total_amount,status,created_at
        FROM invoices";

if($status != ""){
    $sql .= " WHERE status = :status";
}

$sql .= " ORDER BY invoice_id DESC";

$stmt = $pdo->prepare($sql);

if($status != ""){
    $stmt->bindParam(":status",$status);
}

$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';

?>

<div class="container-fluid mt-4">

<h3>Invoices</h3>

<form method="GET">

<select name="status" onchange="this.form.submit()">

<option value="">All Invoices</option>

<option value="completed"
<?=($status=="completed")?"selected":"";?>>
Completed
</option>

<option value="pending"
<?=($status=="pending")?"selected":"";?>>
Pending
</option>

</select>

</form>

<br>

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>Invoice No</th>
<th>Date</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php foreach($invoices as $row){ ?>

<tr>

<td><?= $row['invoice_no']; ?></td>

<td><?= $row['created_at']; ?></td>

<td>Rs. <?= number_format($row['total_amount'],2); ?></td>

<td>

<?php
if($row['status']=="completed"){
    echo "<span class='badge bg-success'>Completed</span>";
}else{
    echo "<span class='badge bg-warning'>Pending</span>";
}
?>

</td>

<td>

<a href="view.php?invoice_no=<?= $row['invoice_no']; ?>"
class="btn btn-primary btn-sm">

View

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php require_once '../../includes/footer.php'; ?>