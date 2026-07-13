<?php

$page = "categories";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

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
            <h2>Categories Management</h2>
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
                placeholder="Search Product..."
            >

        </div>

        <div>

            <button class="add-product-btn" onclick="openModal()">
                + Add Product
            </button>

        </div>

    </div>

    <!-- Summary Cards -->

    <div class="summary-cards">

        <div class="summary-card">
            <h3>125</h3>
            <span>Total Products</span>
        </div>

        <div class="summary-card">
            <h3>110</h3>
            <span>In Stock</span>
        </div>

        <div class="summary-card">
            <h3>12</h3>
            <span>Low Stock</span>
        </div>

        <div class="summary-card">
            <h3>3</h3>
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

                <th>Buying Price</th>

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
                            p.buying_price,
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

                        $query = "
                        SELECT 
                            p.product_id,
                            p.product_code,
                            p.product_name,
                            p.buying_price,
                            p.selling_price,
                            p.stock_qty,
                            p.unit,
                            p.status,
                            c.category_name

                        FROM products p

                        JOIN categories c
                        ON p.category_id = c.category_id
                        ";

                        $stmt = $pdo->prepare($query);

                        $stmt->execute();

                    }

                    

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    ?>

                    <tr>

                        <td><?= $row['product_code']; ?></td>

                        <td><?= $row['product_name']; ?></td>

                        <td><?= $row['category_name']; ?></td>

                        <td><?= $row['buying_price']; ?></td>

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
                                class="edit-btn"
                                onclick="openEditModal(<?= $row['product_id']; ?>)">
                                ✏️ Edit
                            </button>
                        
                            <button 
                                type="button"
                                class="delete-btn"
                                onclick="openDeleteModal(<?= $row['product_id']; ?>)">
                                🗑 Delete
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

<div id="productModal" class="modal">

    <div class="modal-box">

        <span class="close" onclick="closeModal()">&times;</span>

        <iframe
            id="productFrame"
            src="about:blank"
            width="100%"
            height="650"
            frameborder="0">
        </iframe>

    </div>

</div>
<div id="deleteModal" class="delete-modal">

    <div class="delete-box">

        <h3>Delete Product?</h3>

        <p>Are you sure you want to delete this product?</p>


        <button 
        class="delete-confirm"
        onclick="confirmDelete()">
        Yes, Delete
        </button>


        <button 
        class="cancel-delete"
        onclick="closeDeleteModal()">
        Cancel
        </button>


    </div>

</div>

<script>

function openModal(){

    document.getElementById("productFrame").src = "add_product.php";

    document.getElementById("productModal").style.display = "flex";

}

function openEditModal(id){

    document.getElementById("productFrame").src =
    "edit_product.php?id=" + id;

    document.getElementById("productModal").style.display = "flex";

}

function closeModal(){

    document.getElementById("productModal").style.display = "none";

    document.getElementById("productFrame").src = "about:blank";

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

let deleteId = 0;


function openDeleteModal(id){

    deleteId = id;

    document.getElementById("deleteModal").style.display="flex";

}



function closeDeleteModal(){

    document.getElementById("deleteModal").style.display="none";

}



function confirmDelete(){

    window.location =
    "delete_product.php?id=" + deleteId;

}
</script>

</body>
</html>

<?php require_once '../../includes/footer.php'; ?>