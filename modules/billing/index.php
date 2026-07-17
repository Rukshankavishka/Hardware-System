<?php

$page = "billing";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';



require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();

session_start();


if(!isset($_SESSION['invoice_no'])){

    header("Location: start_bill.php");
    exit();

}


$invoice_no = $_SESSION['invoice_no'];


$stmt = $pdo->prepare(
"SELECT * FROM temp_cart 
WHERE invoice_no = :invoice_no"
);


$stmt->execute([

":invoice_no"=>$invoice_no

]);


$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);



$subtotal = 0;


foreach($cart_items as $item){

    $subtotal += $item['total'];

}


$discount = 0;


$grand_total = $subtotal - $discount;


?>
<!DOCTYPE html>
<html lang="en">

<head>
   
    <title></title>
    <link rel="stylesheet" href="../../assets/css/billing.css">

</head>

    <body>

        <div class="bill-container">


        <div class="shop-header">

            <img src="../../assets/images/logo.png" class="shop-logo">


            <h1>
                THISARU HARDWARE
            </h1>

            <h3>
                Hardware & Building Materials
            </h3>


            <p>
                No.20, Nuwara Eliya Rd,
                Nawakadadora, Pussellawa
            </p>


            <p>
                Tel: 0725342110 |
                0712839006 |
                0812077337
            </p>


        </div>


        <hr>



        <div class="bill-info">


            <div>
            Customer :

            <input type="text"
            class="customer-input">

            </div>


            <div>

            Invoice No :
            <b><?= $invoice_no ?></b>

            </div>


            <div>

            Date :
            <?= date("Y-m-d H:i") ?>

            </div>


        </div>


        <hr>



        <table class="bill-table">

        <thead>

        <tr>

        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
        <th>Action</th>

        </tr>

        </thead>



        <tbody>


        <?php foreach($cart_items as $row){ ?>


        <tr>

        <td>
        <?= $row['product_name']; ?>
        </td>


        <td>
        <?= $row['quantity']." ".$row['unit']; ?>
        </td>


        <td>
        Rs. <?= $row['selling_price']; ?>
        </td>


        <td>
        Rs. <?= $row['total']; ?>
        </td>


        <td>

        <button onclick="removeItem(<?= $row['cart_id']; ?>)">
        ❌ Remove
        </button>

        </td>


        </tr>


        <?php } ?>


        </tbody>

        </table>



        <hr>


        <div class="summary">


        <p>
        Subtotal :

        Rs. <?= number_format($subtotal,2); ?>

        </p>



        <p>

        Discount :

        <input 
        type="number"
        id="discount"
        value="0">

        </p>



        <h3>

        Grand Total :

        Rs.

        <span id="grandTotal">

        <?= number_format($grand_total,2); ?>

        </span>

        </h3>


        </div>


        <div class="bill-actions">

            <button onclick="window.print()">
            🖨 Print Bill
            </button>

        </div>

        </div>

        <script>

        document
        .getElementById("discount")
        .addEventListener("keyup",function(){


        let subtotal =
        <?= $subtotal ?>;


        let discount =
        parseFloat(this.value) || 0;



        let total =
        subtotal - discount;



        document.getElementById("grandTotal")
        .innerHTML =
        total.toFixed(2);



        });

        </script>
    </body>
</html>
<?php require_once '../../includes/footer.php'; ?>

