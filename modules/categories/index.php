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

        <button class="category-btn">
            <?php echo $row['category_name']; ?>
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

            </tr>

            </thead>

            <tbody>

                <tbody>
                    <?php

                    $query = "
                    SELECT 
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
            src="add_product.php"
            width="100%"
            height="650"
            frameborder="0">
        </iframe>

    </div>

</div>

<script>

function openModal() {
    document.getElementById("productModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("productModal").style.display = "none";
}

window.onclick = function(event) {

    let modal = document.getElementById("productModal");

    if (event.target == modal) {
        closeModal();
    }
}

</script>

</body>
</html>

<?php require_once '../../includes/footer.php'; ?>