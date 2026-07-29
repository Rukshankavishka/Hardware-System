<?php

$page = "stock";

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";


$stmt = $pdo->query("
SELECT
stock_history.*,
products.product_name

FROM stock_history

INNER JOIN products

ON stock_history.product_id = products.product_id

ORDER BY history_id DESC
");

$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="../../assets/css/stock.css">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">

    <h2>Stock History</h2>

    <a href="index.php" class="back-btn">

        <i class="fa-solid fa-arrow-left"></i>

        Back to Stock

    </a>

</div>

<div class="table-box">

<table>

<thead>

<tr>

<th>Date</th>
<th>Product</th>
<th>Type</th>
<th>Qty</th>
<th>Previous</th>
<th>New</th>
<th>Buying</th>
<th>Remarks</th>

</tr>

</thead>

<tbody>

<?php foreach($history as $row){ ?>

<tr>

<td><?= $row['created_at']; ?></td>

<td><?= $row['product_name']; ?></td>

<td><?= $row['type']; ?></td>

<td><?= $row['quantity']; ?></td>

<td><?= $row['previous_stock']; ?></td>

<td><?= $row['new_stock']; ?></td>

<td>Rs. <?= number_format($row['buying_price'],2); ?></td>

<td><?= $row['remarks']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php require_once "../../includes/footer.php"; ?>