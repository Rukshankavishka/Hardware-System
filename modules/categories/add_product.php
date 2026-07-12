<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


if(isset($_POST['save'])){

    $category_id = $_POST['category_id'];
    $product_name = $_POST['product_name'];
    $product_code = $_POST['product_code'];
    $buying_price = $_POST['buying_price'];
    $selling_price = $_POST['selling_price'];
    $stock_qty = $_POST['stock_qty'];
    $unit = $_POST['unit'];


    $status = "Active";


    $query = "INSERT INTO products
    (
        category_id,
        product_name,
        product_code,
        buying_price,
        selling_price,
        stock_qty,
        unit,
        status
    )

    VALUES
    (
        :category_id,
        :product_name,
        :product_code,
        :buying_price,
        :selling_price,
        :stock_qty,
        :unit,
        :status
    )";


    $stmt = $pdo->prepare($query);


    $stmt->execute([

        ':category_id' => $category_id,
        ':product_name' => $product_name,
        ':product_code' => $product_code,
        ':buying_price' => $buying_price,
        ':selling_price' => $selling_price,
        ':stock_qty' => $stock_qty,
        ':unit' => $unit,
        ':status' => $status

    ]);


    echo "<script>
            alert('Product Added Successfully');
            window.location='index.php';
          </script>";

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>

    <link rel="stylesheet" href="../../assets/css/categories.css">
</head>

<body>

<div class="add-product-container">

    <h2>Add New Product</h2>

    <form method="POST" class="product-form">

    <div class="form-grid">

        <div class="form-group">
            <label>Category</label>

            <select name="category_id" required>

                <?php
                $stmt = $pdo->prepare("SELECT * FROM categories WHERE status='Active'");
                $stmt->execute();

                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                ?>

                <option value="<?= $row['category_id']; ?>">
                    <?= $row['category_name']; ?>
                </option>

                <?php } ?>

            </select>
        </div>

        <div class="form-group">
            <label>Product Code</label>
            <input type="text" name="product_code" required>
        </div>

        <div class="form-group full-width">
            <label>Product Name</label>
            <input type="text" name="product_name" required>
        </div>

        <div class="form-group">
            <label>Buying Price</label>
            <input type="number" step="0.01" name="buying_price" required>
        </div>

        <div class="form-group">
            <label>Selling Price</label>
            <input type="number" step="0.01" name="selling_price" required>
        </div>

        <div class="form-group">
            <label>Stock Qty</label>
            <input type="number" name="stock_qty" required>
        </div>

        <div class="form-group">
            <label>Unit</label>
            <input type="text" name="unit" required>
        </div>

    </div>

    <button type="submit" name="save" class="save-btn">
        Save Product
    </button>

</form>

</div>

</body>
</html>