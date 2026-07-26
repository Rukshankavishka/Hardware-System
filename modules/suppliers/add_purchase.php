<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


$supplier_id = $_GET['supplier_id'];


$stmt = $pdo->prepare(
"SELECT * FROM suppliers 
WHERE supplier_id=:id"
);

$stmt->execute([
":id"=>$supplier_id
]);


$supplier = $stmt->fetch(PDO::FETCH_ASSOC);



/* Products Load */

$product_stmt = $pdo->query(
"SELECT * FROM products 
ORDER BY product_name ASC"
);


$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);



?>


<!DOCTYPE html>

<html>

<head>

<title>Add Purchase</title>

<link rel="stylesheet"
href="../../assets/css/suppliers.css">

</head>


<body>


<div class="purchase-container">


<h2>
Add Purchase
</h2>


<p>
Supplier :
<b><?= $supplier['supplier_name']; ?></b>
</p>



<form action="save_purchase.php" method="POST">


<input type="hidden"
name="supplier_id"
value="<?= $supplier_id; ?>">



<label>
Product
</label>


<select name="product_id" required>


<option value="">
Select Product
</option>


<?php foreach($products as $p){ ?>


<option value="<?= $p['product_id']; ?>">

<?= $p['product_name']; ?>

</option>


<?php } ?>


</select>




<label>
Quantity
</label>

<input type="number"
name="quantity"
step="0.01"
required>




<label>
Buying Price
</label>


<input type="number"
name="buying_price"
step="0.01"
required>



<label>
Paid Amount
</label>


<input type="number"
name="paid_amount"
value="0"
step="0.01">



<button class="save-btn">

Save Purchase

</button>


</form>


</div>


</body>

</html>