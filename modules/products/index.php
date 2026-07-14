
<?php

$page = "products";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

$category_id = $_GET['category_id'] ?? null;


// Base condition

$where = "";

$params = [];


if($category_id){

    $where = " WHERE category_id = :category_id ";

    $params[':category_id'] = $category_id;

}


// Total Products

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM products $where"
);

$stmt->execute($params);

$totalProducts = $stmt->fetchColumn();



// In Stock

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM products 
     $where 
     " . ($where ? " AND " : " WHERE ") . " stock_qty > 0"
);

$stmt->execute($params);

$inStock = $stmt->fetchColumn();



// Low Stock

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM products 
     $where 
     " . ($where ? " AND " : " WHERE ") . " stock_qty <= 10"
);

$stmt->execute($params);

$lowStock = $stmt->fetchColumn();



// Out Stock

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM products 
     $where 
     " . ($where ? " AND " : " WHERE ") . " stock_qty = 0"
);

$stmt->execute($params);

$outStock = $stmt->fetchColumn();


?>


<!DOCTYPE html>
<html lang="en">

<head>
   
    <title></title>
    <link rel="stylesheet" href="../../assets/css/categories.css">

</head>

<body>

<div class="categories-page">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2>Products</h2>
        </div>
    </div>

    <!-- Category Buttons -->
    <div class="category-buttons">

        <?php

        $query = "SELECT * FROM categories WHERE status='Active'";

        $stmt = $pdo->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        ?>

        <button 
            class="category-btn"
            onclick="filterCategory(<?= $row['category_id']; ?>)">

            <?= $row['category_name']; ?>

        </button>

        <?php

        }

        ?>

    </div>

    <!-- Search & Add Product -->

    <div class="category-toolbar">

        <div class="search-box">

            <input
                type="text"
                id="searchProduct"
                placeholder="Search Product..."
            >

        </div>

    </div>

    <!-- Summary Cards -->

    <div class="summary-cards">

        <div class="summary-card">
            <h3><?= $totalProducts; ?></h3>
            <span>Total Products</span>
        </div>

        <div class="summary-card">
            <h3><?= $inStock; ?></h3>
            <span>In Stock</span>
        </div>

        <div class="summary-card">
            <h3><?= $lowStock; ?></h3>
            <span>Low Stock</span>
        </div>

        <div class="summary-card">
            <h3><?= $outStock; ?></h3>
            <span>Out of Stock</span>
        </div>

    </div>

    <!-- Product Table -->

    <div class="table-container">

        <table>

            <thead>

            <tr>

                <th>Product Code</th>

                <th>Product Name</th>

                <th>Category</th>

                <th>Selling Price</th>

                <th>Stock Qty</th>

                <th>Unit</th>

                <th>Status</th>

                <th>Action</th>


            </tr>

            </thead>

            <tbody>

                <tbody>
                    <?php

                    if(isset($_GET['category_id'])){

                        $category_id = $_GET['category_id'];

                        $query = "
                        SELECT 
                            p.product_id,
                            p.product_code,
                            p.product_name,
                            p.selling_price,
                            p.stock_qty,
                            p.unit,
                            p.status,
                            c.category_name

                        FROM products p

                        JOIN categories c
                        ON p.category_id = c.category_id

                        WHERE p.category_id = :category_id
                        ";

                        $stmt = $pdo->prepare($query);

                        $stmt->execute([
                            ':category_id' => $category_id
                        ]);


                    }else{

                        $search = "";

                        if(isset($_GET['search'])){
                            $search = $_GET['search'];
                        }


                        $query = "
                        SELECT 
                            p.product_id,
                            p.product_code,
                            p.product_name,
                            p.selling_price,
                            p.stock_qty,
                            p.unit,
                            p.status,
                            c.category_name

                        FROM products p

                        JOIN categories c
                        ON p.category_id = c.category_id

                        WHERE 
                        p.product_name LIKE :search
                        OR
                        p.product_code LIKE :search
                        ";


                        $stmt = $pdo->prepare($query);


                        $stmt->execute([
                            ':search' => "%$search%"
                        ]);

                    }

                    

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    ?>

                    <tr>

                        <td><?= $row['product_code']; ?></td>

                        <td><?= $row['product_name']; ?></td>

                        <td><?= $row['category_name']; ?></td>

                        <td><?= $row['selling_price']; ?></td>

                        <td><?= $row['stock_qty']; ?></td>

                        <td><?= $row['unit']; ?></td>

                        <td>
                            <span class="status">
                                <?= $row['status']; ?>
                            </span>
                        </td>

                        <td>
                            <button
                                type="button"
                                class="bill-btn"
                                onclick="openBillModal(
                                '<?= $row['product_name']; ?>',
                                <?= $row['selling_price']; ?>,
                                <?= $row['product_id']; ?>
                                )">

                                🛒 Add Bill

                            </button>
                        </td>

                    </tr>

                    <?php
                    }
                    ?>

                    </tbody>
        </table>

    </div>
</div>

<div id="billModal" class="modal">

    <div class="modal-box">

        <span class="close" onclick="closeBillModal()">
            &times;
        </span>


        <h2>Add Product To Bill</h2>


        <div class="form-group">

            <label>Product Name</label>

            <input type="text" id="billProduct" readonly>

        </div>


        <div class="form-group">

            <label>Price</label>

            <input type="number" id="billPrice">

        </div>


        <div class="form-group">

            <label>Quantity</label>

            <input type="number" id="billQty" value="1">

        </div>


        <button class="bill-btn">
            Add To Bill
        </button>


    </div>

</div>

<script>

let selectedProductId;


function openBillModal(name, price, id){

    selectedProductId = id;


    document.getElementById("billProduct").value = name;


    document.getElementById("billPrice").value = price;


    document.getElementById("billModal").style.display="flex";

}


function closeBillModal(){

    document.getElementById("billModal").style.display="none";

}

window.onclick = function(event){

    let modal = document.getElementById("productModal");

    if(event.target == modal){

        closeModal();

    }

}
function filterCategory(id){

    window.location =
    "index.php?category_id=" + id;

}

document.getElementById("searchProduct").addEventListener("keyup", function(){

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll(".table-container tbody tr");


    rows.forEach(function(row){

        let text = row.innerText.toLowerCase();


        if(text.includes(value)){

            row.style.display = "";

        }else{

            row.style.display = "none";

        }

    });

});

</script>

</body>
</html>

<?php require_once '../../includes/footer.php'; ?>