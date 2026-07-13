<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


// GET PRODUCT ID

if(isset($_GET['id'])){

    $id = $_GET['id'];


    $stmt = $pdo->prepare(
        "SELECT * FROM products WHERE product_id = :id"
    );


    $stmt->execute([
        ':id'=>$id
    ]);


    $product = $stmt->fetch(PDO::FETCH_ASSOC);

}



// UPDATE PRODUCT

if(isset($_POST['update'])){


    $product_id = $_GET['id'];


    $category_id = $_POST['category_id'];
    $product_name = $_POST['product_name'];
    $product_code = $_POST['product_code'];
    $buying_price = $_POST['buying_price'];
    $selling_price = $_POST['selling_price'];
    $stock_qty = $_POST['stock_qty'];
    $unit = $_POST['unit'];



    $query = "UPDATE products SET

        category_id = :category_id,
        product_name = :product_name,
        product_code = :product_code,
        buying_price = :buying_price,
        selling_price = :selling_price,
        stock_qty = :stock_qty,
        unit = :unit

        WHERE product_id = :product_id";


    $stmt = $pdo->prepare($query);



    $stmt->execute([

        ':category_id'=>$category_id,
        ':product_name'=>$product_name,
        ':product_code'=>$product_code,
        ':buying_price'=>$buying_price,
        ':selling_price'=>$selling_price,
        ':stock_qty'=>$stock_qty,
        ':unit'=>$unit,
        ':product_id'=>$product_id

    ]);



    echo "

    <script>

        alert('Product Updated Successfully');

        window.parent.closeModal();

        window.parent.location.reload();

    </script>

    ";


    exit;

}


?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Edit Product</title>

<link rel="stylesheet" href="../../assets/css/categories.css">

</head>


<body>


<div class="add-product-container">


<h2>Edit Product</h2>



<form method="POST" class="product-form">


<div class="form-grid">



<div class="form-group">

<label>Category</label>


<select name="category_id" required>


<?php


$stmt = $pdo->prepare(
    "SELECT * FROM categories WHERE status='Active'"
);

$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)){


?>


<option value="<?= $row['category_id']; ?>"
    
<?= ($row['category_id'] == $product['category_id']) ? 'selected' : ''; ?>

>

<?= $row['category_name']; ?>


</option>


<?php } ?>


</select>


</div>




<div class="form-group">

<label>Product Code</label>


<input type="text"
name="product_code"
value="<?= $product['product_code']; ?>"
required>


</div>




<div class="form-group full-width">

<label>Product Name</label>


<input type="text"
name="product_name"
value="<?= $product['product_name']; ?>"
required>


</div>




<div class="form-group">

<label>Buying Price</label>


<input type="number"
step="0.01"
name="buying_price"
value="<?= $product['buying_price']; ?>"
required>


</div>




<div class="form-group">

<label>Selling Price</label>


<input type="number"
step="0.01"
name="selling_price"
value="<?= $product['selling_price']; ?>"
required>


</div>




<div class="form-group">

<label>Stock Qty</label>


<input type="number"
name="stock_qty"
value="<?= $product['stock_qty']; ?>"
required>


</div>




<div class="form-group">

<label>Unit</label>


<input type="text"
name="unit"
value="<?= $product['unit']; ?>"
required>


</div>



</div>



<button type="submit" name="update" class="save-btn">

Update Product

</button>



</form>



</div>



</body>

</html>