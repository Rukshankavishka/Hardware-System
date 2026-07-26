
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
                                onclick='openBillModal(
                                <?= json_encode($row["product_name"]); ?>,
                                <?= $row["selling_price"]; ?>,
                                <?= json_encode($row["unit"]); ?>,
                                <?= $row["stock_qty"]; ?>,
                                <?= $row["product_id"]; ?>
                                )'>

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
                <label>Product</label>
                <input type="text" id="billProduct" readonly>
            </div>

            <div class="form-group">
                <label>Original Price (Rs.)</label>
                <input type="number" id="originalPrice" readonly>
            </div>

            <div class="form-group">
                <label>Special Price (Rs.)</label>
                <input type="number"
                    id="billPrice"
                    onkeyup="calculateTotal()">
            </div>

            <div class="form-group">

                <label>Quantity</label>

                <div class="qty-box">
                    <input type="number"
                        id="billQty"
                        value="1.00"
                        step="0.01"
                        onkeyup="calculateTotal()">
                </div>

            </div>
            <div class="form-group">

                <label>Current Stock</label>

                <input type="text"
                    id="billStock"
                    readonly>

            </div>
            <div class="form-group">

                <label>Unit</label>

                <input type="text"
                id="billUnit"
                readonly>

            </div>

            <div class="bill-total">

                Total :
                <span>Rs. <span id="billTotal">0.00</span></span>

            </div>

            <div class="bill-buttons">

                <button type = "button" class="bill-btn"
                        onclick="addToBill()">

                    🛒 Add To Bill

                </button>

                <button class="cancel-btn"
                        onclick="closeBillModal()">

                    Cancel

                </button>

            </div>

    </div>

</div>

<script>

let selectedProductId = 0;

function addToBill(){

    let formData = new FormData();

    formData.append("product_id", selectedProductId);

    formData.append(
        "product_name",
        document.getElementById("billProduct").value
    );

    formData.append(
        "unit",
        document.getElementById("billUnit").value
    );

    formData.append(
        "quantity",
        document.getElementById("billQty").value
    );

    formData.append(
        "original_price",
        document.getElementById("originalPrice").value
    );

    formData.append(
        "selling_price",
        document.getElementById("billPrice").value
    );

    formData.append(
        "total",
        document.getElementById("billTotal").innerText
    );


    fetch("add_bill.php",{

        method:"POST",

        body:formData

    })

    .then(response => response.text())

    .then(data => {

        console.log(data);


        if(data.trim()=="success"){

            alert("Product Added To Bill");

            closeBillModal();

        }
        else{

            alert(data);

        }

    });

}

function openBillModal(name, price, unit, stock, id){

    selectedProductId = id;

    document.getElementById("billProduct").value = name;

    document.getElementById("billUnit").value = unit;

    document.getElementById("billStock").value = stock + " " + unit;

    document.getElementById("originalPrice").value = price;

    document.getElementById("billPrice").value = price;

    document.getElementById("billQty").value = "1.00";

    calculateTotal();

    document.getElementById("billModal").style.display="flex";

}

function closeBillModal(){

    document.getElementById("billModal").style.display = "none";

}

function increaseQty(){

    let qty = document.getElementById("billQty");

    qty.value = parseInt(qty.value) + 1;

    calculateTotal();

}

function decreaseQty(){

    let qty = document.getElementById("billQty");

    if(parseInt(qty.value) > 1){

        qty.value = parseInt(qty.value) - 1;

    }

    calculateTotal();

}

function calculateTotal(){

    let price = parseFloat(
        document.getElementById("billPrice").value
    ) || 0;


    let qty = parseFloat(
        document.getElementById("billQty").value
    ) || 0;


    let total = price * qty;


    document.getElementById("billTotal").innerHTML =
    total.toFixed(2);

}

window.onclick = function(event){

    let modal = document.getElementById("billModal");

    if(event.target == modal){

        closeBillModal();

    }

}

function filterCategory(id){

    window.location = "index.php?category_id=" + id;

}

let search = document.getElementById("searchProduct");

if(search){

    search.addEventListener("keyup", function(){

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

}

</script>

</body>
</html>

<?php require_once '../../includes/footer.php'; ?>