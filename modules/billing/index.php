<?php

$page = "billing";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


session_start();

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();


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

<div class="bill-container">


<h2>
THISARU HARDWARE & BUILDING MATERIALS
</h2>


<p>
No.20, Nuwara Eliya Rd,
Nawakadadora, Pussellawa
</p>


<p>
Tel: 0725342110 | 0712839006 | 0812077337
</p>


<hr>



<div>

Customer :

<input type="text">


</div>


<div>

Invoice No :

<?= $invoice_no ?>

</div>


<div>

Date :

<?= date("Y-m-d") ?>

</div>


<hr>



<table>

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


<button>
Print Bill
</button>

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

<?php require_once '../../includes/footer.php'; ?>

