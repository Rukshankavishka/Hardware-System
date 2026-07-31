<?php

require_once '../../includes/auth.php';

$page = "stock";

require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


// Stock Data

$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("

SELECT
products.*,
categories.category_name

FROM products

LEFT JOIN categories

ON products.category_id = categories.category_id

WHERE

product_name LIKE :search

OR

product_code LIKE :search

ORDER BY product_id DESC

");

$stmt->execute([

":search"=>"%".$search."%"

]);


$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Cards

$total_products = count($products);

$total_stock = 0;
$low_stock = 0;
$out_stock = 0;


foreach($products as $p){

    $total_stock += $p['stock_qty'];

    if($p['stock_qty'] <= 5 && $p['stock_qty'] > 0){
        $low_stock++;
    }

    if($p['stock_qty'] == 0){
        $out_stock++;
    }

}


?>


<link rel="stylesheet" href="../../assets/css/stock.css">


        <div class="container-fluid">


            <h2>Stock Management</h2>


            <div class="cards">


                    <div class="card">
                        <h4>Total Products</h4>
                        <h2><?= $total_products ?></h2>
                    </div>


                    <div class="card">
                        <h4>Total Stock</h4>
                        <h2><?= $total_stock ?></h2>
                    </div>


                    <div class="card">
                        <h4>Low Stock</h4>
                        <h2><?= $low_stock ?></h2>
                    </div>


                    <div class="card">
                        <h4>Out Of Stock</h4>
                        <h2><?= $out_stock ?></h2>
                    </div>


            </div>



        <div class="table-box">

    <div class="search-box">

        <form method="GET">

            <input
            type="text"
            name="search"
            placeholder="Search Product Name or Code..."
            value="<?= $_GET['search'] ?? '' ?>">

            <button type="submit">

            <i class="fa-solid fa-magnifying-glass"></i>
                Search
            </button>

        </form>

    </div>

<table>

        <thead>

            <tr>

                <th>Code</th>
                <th>Product</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Unit</th>
                <th>Buying</th>
                <th>Selling</th>
                <th>Status</th>
                <th>Action</th>

            </tr>

        </thead>


        <tbody>


        <?php foreach($products as $row){ ?>


                <tr>

                    <td><?= $row['product_code']; ?></td>

                    <td><?= $row['product_name']; ?></td>

                    <td><?= $row['category_name']; ?></td>

                    <td><?= $row['stock_qty']; ?></td>

                    <td><?= $row['unit']; ?></td>

                    <td><?= $row['buying_price']; ?></td>

                    <td><?= $row['selling_price']; ?></td>


                    <td>

                    <?php

                    if($row['stock_qty']==0){

                    echo "<span class='out'>Out</span>";

                    }
                    elseif($row['stock_qty']<=5){

                    echo "<span class='low'>Low</span>";

                    }
                    else{

                    echo "<span class='available'>Available</span>";

                    }

                    ?>


                    </td>

                    <td>

                            <button class="btn-add" onclick="openStockPopup(<?= $row['product_id']; ?>)">

                            <i class="fa-solid fa-plus"></i>

                            Add Stock

                            </button>

                    
                        <a href="stock_history.php?id=<?= $row['product_id']; ?>" 
                        class="btn-history">

                        <i class="fa-solid fa-clock-rotate-left"></i>
                        History

                        </a>


                    </td>


                </tr>


        <?php } ?>


        </tbody>

</table>


</div>


</div>

    <div class="popup" id="stockPopup">

        <div class="popup-content">

            <span class="close" onclick="closeStockPopup()">&times;</span>

            <iframe
                id="stockFrame"
                width="100%"
                height="500"
                frameborder="0">
            </iframe>

        </div>

    </div>

<script>
    function openStockPopup(id){

        document.getElementById("stockPopup").style.display="flex";

        document.getElementById("stockFrame").src=
        "add_stock.php?id="+id;

        }

        function closeStockPopup(){

        document.getElementById("stockPopup").style.display="none";

        document.getElementById("stockFrame").src="";

        location.reload();

        }
</script>



<?php require_once "../../includes/footer.php"; ?>