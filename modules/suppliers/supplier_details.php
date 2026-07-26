<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

$product_stmt = $pdo->query("
SELECT product_id, product_name
FROM products
ORDER BY product_name ASC
");

$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

if(!isset($_GET['id'])){

    die("Supplier Not Found");

}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id=:id");

$stmt->execute([":id"=>$id]);

$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$supplier){

    die("Supplier Not Found");

}

$stmt = $pdo->prepare("
SELECT *
FROM purchase_history
WHERE supplier_id = :supplier_id
ORDER BY purchase_date DESC
");

$stmt->execute([
":supplier_id"=>$id
]);

$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_purchase = 0;
$total_paid = 0;
$total_balance = 0;

foreach($purchases as $row){

    $total_purchase += $row['total'];
    $total_paid += $row['paid_amount'];
    $total_balance += $row['balance'];

}

?>

<!DOCTYPE html>

<html>

<head>

<link rel="stylesheet"href="../../assets/css/suppliers.css">

</head>

<body>

<div class="top-buttons">

<button class="back-btn" onclick="history.back()">
← Back
</button>

<button type="button" class="add-btn" onclick="openPurchaseModal()">
+ Add Purchase
</button>

</div>

<div class="info-grid">

<div class="info-card">
<label>Supplier Code</label>
<span><?= $supplier['supplier_code']; ?></span>
</div>

<div class="info-card">
<label>Supplier Name</label>
<span><?= $supplier['supplier_name']; ?></span>
</div>

<div class="info-card">
<label>Company</label>
<span><?= $supplier['company_name']; ?></span>
</div>

<div class="info-card">
<label>Phone</label>
<span><?= $supplier['phone']; ?></span>
</div>

<div class="info-card">
<label>Address</label>
<span><?= $supplier['address']; ?></span>
</div>

<div class="info-card">
<label>Business Registration</label>
<span><?= $supplier['br_no']; ?></span>
</div>

</div>

<hr>

<h2>Purchase History</h2>

<table class="purchase-table">

    <thead>

        <tr>

            <th>Date</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Buying Price</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Action</th>

        </tr>

    </thead>

    <tbody>

    <?php foreach($purchases as $row){ ?>

        <tr>

            <td><?= $row['purchase_date']; ?></td>

            <td><?= $row['product_name']; ?></td>

            <td><?= $row['quantity']; ?></td>

            <td>Rs. <?= number_format($row['buying_price'],2); ?></td>

            <td>Rs. <?= number_format($row['total'],2); ?></td>

            <td>Rs. <?= number_format($row['paid_amount'],2); ?></td>

            <td>Rs. <?= number_format($row['balance'],2); ?></td>

            <td>
                <button class="payment-btn" onclick="openPaymentModal( <?= $row['purchase_id']; ?>, <?= $row['balance']; ?> )">
                    💰 Payment
                </button>
            </td>
        </tr>

    <?php } ?>

    </tbody>

</table>

<br>

<div class="summary-box">

    <h3>Total Purchase : Rs. <?= number_format($total_purchase,2); ?></h3>

    <h3>Total Paid : Rs. <?= number_format($total_paid,2); ?></h3>

    <h2>Balance Due : Rs. <?= number_format($total_balance,2); ?></h2>

</div>


<div id="purchaseModal" class="modal">

    <div class="modal-box">

        <span class="close"
        onclick="closePurchaseModal()">

        &times;

        </span>

        <h2>Add Purchase</h2>

        <form action="save_purchase.php" method="POST">

            <input type="hidden" name="supplier_id" value="<?= $supplier['supplier_id']; ?>">

            <label>Product</label>

            <select id="purchaseProduct" name="product_id" required>

                <option value="">Select Product</option>

                <?php foreach($products as $product){ ?>

                    <option value="<?= $product['product_id']; ?>">

                        <?= $product['product_name']; ?>

                    </option>

                <?php } ?>

            </select>

            <label>Quantity</label>

            <input type="number" id="purchaseQty" name="quantity">

            <label>Buying Price</label>

            <input type="number" id="purchasePrice" name="buying_price">

            <label>Paid Amount</label>

            <input type="number" id="purchasePaid" name="paid_amount">

            <div class="bill-buttons">

                <button type="submit" class="bill-btn">
                    Save Purchase
                </button>

                <button type="button" class="cancel-btn" onclick="closePurchaseModal()">
                    Cancel
                </button>

            </div>

        </form>

    </div>

</div>

<div id="paymentModal" class="modal">

    <div class="modal-box">

        <span class="close"
        onclick="closePaymentModal()">

        &times;

        </span>

        <h2>Supplier Payment</h2>

        <form action="save_payment.php" method="POST">

            <input
            type="hidden"
            name="purchase_id"
            id="purchaseId">

            <label>Current Balance</label>

            <input
            type="number"
            id="currentBalance"
            readonly>

            <label>Payment Amount</label>

            <input
            type="number"
            name="payment_amount"
            required>

            <br><br>

            <button
            type="submit"
            class="bill-btn">

            Save Payment

            </button>

        </form>

    </div>

</div>

<script>

    function openPurchaseModal(){

        document.getElementById("purchaseModal").style.display="flex";

    }

    function closePurchaseModal(){

        document.getElementById("purchaseModal").style.display="none";

    }

    function openPaymentModal(id,balance){

    document.getElementById("purchaseId").value=id;

    document.getElementById("currentBalance").value=balance;

    document.getElementById("paymentModal").style.display="flex";

    }

    function closePaymentModal(){

        document.getElementById("paymentModal").style.display="none";

    }

</script>
</body>

</html>