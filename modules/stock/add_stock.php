<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

$product_id = $_GET['id'];

$stmt = $pdo->prepare("
SELECT *
FROM products
WHERE product_id = :id
");

$stmt->execute([
":id"=>$product_id
]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Add Stock</title>

<link rel="stylesheet"
href="../../assets/css/stock.css">

<style>

body{

padding:20px;
font-family:Arial;

}

.form-group{

margin-bottom:15px;

}

label{

font-weight:bold;
display:block;
margin-bottom:5px;

}

input,
textarea{

width:100%;
padding:10px;
border:1px solid #ccc;
border-radius:8px;

}

button{

width:100%;
padding:12px;
background:#198754;
color:white;
border:none;
border-radius:8px;
font-size:16px;
cursor:pointer;

}

button:hover{

background:#157347;

}

</style>

</head>

<body>

<h3>Add Stock</h3>

<form action="save_stock.php" method="POST">

<input type="hidden"
name="product_id"
value="<?= $product['product_id']; ?>">

<div class="form-group">

<label>Product</label>

<input type="text"
value="<?= $product['product_name']; ?>"
readonly>

</div>

<div class="form-group">

<label>Current Stock</label>

<input type="text"
value="<?= $product['stock_qty']; ?>"
readonly>

</div>

<div class="form-group">

<label>Quantity</label>

<input
type="number"
name="quantity"
required>

</div>

<div class="form-group">

<label>Buying Price</label>

<input
type="number"
step="0.01"
name="buying_price"
required>

</div>

<div class="form-group">

<label>Remarks</label>

<textarea
name="remarks"></textarea>

</div>

<button type="submit">

Save Stock

</button>

</form>

</body>

</html>