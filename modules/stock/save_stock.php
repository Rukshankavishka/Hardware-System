<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

$product_id    = $_POST['product_id'];
$quantity      = $_POST['quantity'];
$buying_price  = $_POST['buying_price'];
$remarks       = $_POST['remarks'];

try{

    $pdo->beginTransaction();

    // Current Stock
    $stmt = $pdo->prepare("
        SELECT stock_qty
        FROM products
        WHERE product_id = :id
    ");

    $stmt->execute([
        ":id"=>$product_id
    ]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $previous_stock = $product['stock_qty'];
    $new_stock = $previous_stock + $quantity;

    // Update Product
    $stmt = $pdo->prepare("
        UPDATE products
        SET
            stock_qty = :stock,
            buying_price = :buying_price
        WHERE product_id = :id
    ");

    $stmt->execute([

        ":stock"=>$new_stock,
        ":buying_price"=>$buying_price,
        ":id"=>$product_id

    ]);

    // Save History
    $stmt = $pdo->prepare("
        INSERT INTO stock_history
        (
            product_id,
            type,
            quantity,
            previous_stock,
            new_stock,
            buying_price,
            remarks
        )
        VALUES
        (
            :product_id,
            'IN',
            :quantity,
            :previous_stock,
            :new_stock,
            :buying_price,
            :remarks
        )
    ");

    $stmt->execute([

        ":product_id"=>$product_id,
        ":quantity"=>$quantity,
        ":previous_stock"=>$previous_stock,
        ":new_stock"=>$new_stock,
        ":buying_price"=>$buying_price,
        ":remarks"=>$remarks

    ]);

    $pdo->commit();

    echo "
    <script>

        alert('Stock Added Successfully');

        parent.closeStockPopup();

    </script>
    ";

}catch(Exception $e){

    $pdo->rollBack();

    echo $e->getMessage();

}