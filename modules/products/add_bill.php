<?php

session_start();

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

$product_id     = $_POST['product_id'];
$product_name   = $_POST['product_name'];
$unit           = $_POST['unit'];
$quantity       = $_POST['quantity'];
$original_price = $_POST['original_price'];
$selling_price  = $_POST['selling_price'];
$total          = $_POST['total'];

$invoice_no = $_SESSION['invoice_no'];
$cashier_id = 1;

$query = "INSERT INTO temp_cart
(
    invoice_no,
    cashier_id,
    product_id,
    product_name,
    unit,
    quantity,
    original_price,
    selling_price,
    total
)

VALUES
(
    :invoice_no,
    :cashier_id,
    :product_id,
    :product_name,
    :unit,
    :quantity,
    :original_price,
    :selling_price,
    :total
)";

$stmt = $pdo->prepare($query);

$stmt->execute([

    ':invoice_no' => $invoice_no,
    ':cashier_id' => $cashier_id,
    ':product_id' => $product_id,
    ':product_name' => $product_name,
    ':unit' => $unit,
    ':quantity' => $quantity,
    ':original_price' => $original_price,
    ':selling_price' => $selling_price,
    ':total' => $total

]);

echo "success";
?>