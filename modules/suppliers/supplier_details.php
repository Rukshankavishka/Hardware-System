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

/* All payments related to this supplier's purchases (used by View modal) */

$stmt = $pdo->prepare("
    SELECT t.*
    FROM transactions t
    INNER JOIN purchase_history p ON t.reference_id = p.purchase_id
    WHERE p.supplier_id = :supplier_id
    AND t.reference_type = 'Purchase'
    ORDER BY t.transaction_date DESC, t.transaction_id DESC
");

$stmt->execute([
    ":supplier_id" => $id
]);

$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

                <button class="view-btn" onclick="openViewModal( <?= $row['purchase_id']; ?> )">
                    👁 View
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

            <label>Payment Method</label>

            <select
            name="payment_method"
            id="paymentMethod"
            onchange="togglePaymentFields()"
            required>

                <option value="Cash">Cash</option>
                <option value="Cheque">Cheque</option>

            </select>

            <div id="chequeFields" style="display:none;">

                <label>Cheque No</label>
                <input type="text" name="cheque_no" id="chequeNo">

                <label>Bank</label>
                <input type="text" name="cheque_bank" id="chequeBank">

                <label>Cheque Date</label>
                <input type="date" name="cheque_date" id="chequeDate">

            </div>

            <br><br>

            <button
            type="submit"
            class="bill-btn">

            Save Payment

            </button>

        </form>

    </div>

</div>

<div id="viewModal" class="modal">

    <div class="modal-box">

        <span class="close"
        onclick="closeViewModal()">

        &times;

        </span>

        <h2>Payment Details</h2>

        <div id="viewModalBody">
            <!-- JS එකෙන් fill වෙනවා -->
        </div>

    </div>

</div>

<script>

    const allPayments = <?= json_encode($payments); ?>;

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

    togglePaymentFields();

    }

    function closePaymentModal(){

        document.getElementById("paymentModal").style.display="none";

    }

    function togglePaymentFields(){

        const method = document.getElementById("paymentMethod").value;
        const chequeFields = document.getElementById("chequeFields");
        const chequeNo = document.getElementById("chequeNo");
        const chequeBank = document.getElementById("chequeBank");
        const chequeDate = document.getElementById("chequeDate");

        if(method === "Cheque"){
            chequeFields.style.display = "block";
            chequeNo.required = true;
            chequeBank.required = true;
            chequeDate.required = true;
        } else {
            chequeFields.style.display = "none";
            chequeNo.required = false;
            chequeBank.required = false;
            chequeDate.required = false;
        }

    }

    function openViewModal(purchaseId){

        const modalBody = document.getElementById("viewModalBody");

        const rows = allPayments.filter(p => p.reference_id == purchaseId);

        if(rows.length === 0){

            modalBody.innerHTML = "<p>No payments recorded for this purchase yet.</p>";

        } else {

            let html = "";

            rows.forEach(pay => {

                html += `<div class="payment-detail-card">`;
                html += `<p><strong>Date:</strong> ${pay.transaction_date}</p>`;
                html += `<p><strong>Amount:</strong> Rs. ${parseFloat(pay.amount).toFixed(2)}</p>`;

                if(pay.payment_method === "Cheque"){
                    html += `<p><strong>Method:</strong> 🏦 Cheque</p>`;
                    html += `<p><strong>Cheque No:</strong> ${pay.cheque_no ?? '-'}</p>`;
                    html += `<p><strong>Bank:</strong> ${pay.cheque_bank ?? '-'}</p>`;
                    html += `<p><strong>Cheque Date:</strong> ${pay.cheque_date ?? '-'}</p>`;
                } else {
                    html += `<p><strong>Method:</strong> 💵 Cash</p>`;
                }

                html += `</div><hr>`;

            });

            modalBody.innerHTML = html;

        }

        document.getElementById("viewModal").style.display = "flex";

    }

    function closeViewModal(){

        document.getElementById("viewModal").style.display = "none";

    }

</script>
</body>

</html>