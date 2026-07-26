<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


$supplier_id  = $_POST['supplier_id'];
$product_id   = $_POST['product_id'];
$quantity      = $_POST['quantity'];
$buying_price  = $_POST['buying_price'];
$paid_amount   = $_POST['paid_amount'];

$total   = $quantity * $buying_price;
$balance = $total - $paid_amount;


/* Get Product Name */

$stmt = $pdo->prepare("
SELECT product_name
FROM products
WHERE product_id = :product_id
");

$stmt->execute([
    ":product_id"=>$product_id
]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

$product_name = $product['product_name'];


/* Save Purchase */

$stmt = $pdo->prepare("

INSERT INTO purchase_history
(
supplier_id,
product_id,
product_name,
quantity,
buying_price,
total,
paid_amount,
balance,
purchase_date
)

VALUES
(
:supplier_id,
:product_id,
:product_name,
:quantity,
:buying_price,
:total,
:paid_amount,
:balance,
CURDATE()
)

");

$stmt->execute([

":supplier_id"=>$supplier_id,
":product_id"=>$product_id,
":product_name"=>$product_name,
":quantity"=>$quantity,
":buying_price"=>$buying_price,
":total"=>$total,
":paid_amount"=>$paid_amount,
":balance"=>$balance

]);


/* Update Stock */

$stmt = $pdo->prepare("

UPDATE products

SET stock_qty = stock_qty + :quantity

WHERE product_id = :product_id

");

$stmt->execute([

":quantity"=>$quantity,
":product_id"=>$product_id

]);


header("Location:supplier_details.php?id=".$supplier_id);
exit();

?>