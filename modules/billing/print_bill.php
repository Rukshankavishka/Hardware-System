<?php
require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

if(!isset($_GET['invoice_no'])){
    die("Invoice not found");
}

$invoice_no = $_GET['invoice_no'];

// Invoice
$stmt = $pdo->prepare("
SELECT *
FROM invoices
WHERE invoice_no = :invoice_no
");
$stmt->execute([
    ":invoice_no"=>$invoice_no
]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$invoice){
    die("Invoice not found");
}

// Invoice Items
$stmt = $pdo->prepare("
SELECT *
FROM invoice_items
WHERE invoice_no = :invoice_no
");
$stmt->execute([
    ":invoice_no"=>$invoice_no
]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optional fields (won't break if columns don't exist in your table)
$customer      = $invoice['customer_name']   ?? null;
$paymentMethod = $invoice['payment_method']  ?? null;
$paidAmount    = $invoice['paid_amount']     ?? null;
$balance       = $invoice['balance']         ?? null;

// Totals (discount % per item, same logic as index.php)
$subtotal      = 0;
$totalDiscount = 0;

foreach($items as $row){
    $qty      = (float)$row['quantity'];
    $price    = (float)$row['selling_price'];
    $discount = (float)($row['discount'] ?? 0);

    $lineSub  = $qty * $price;
    $lineDisc = $lineSub * ($discount / 100);

    $subtotal      += $lineSub;
    $totalDiscount += $lineDisc;
}

$grandTotal = $subtotal - $totalDiscount;
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Invoice #<?= htmlspecialchars($invoice['invoice_no']); ?></title>

    <link rel="stylesheet" href="../../assets/css/billing.css">
    <link rel="stylesheet" href="../../assets/css/print_bill.css">

</head>

<body>

    <div class="bill-container">

        <div class="shop-header">
            <div class="shop-header-logo">
                <h1>
                    <img src="../../assets/images/logo.png" class="shop-logo">
                    THISARU
                </h1>
            </div>

            <p>Hardware &amp; Building Materials</p>
            <p>No.20, Nuwara Eliya Rd, Nawakadadora, Pussellawa</p>
            <p>Tel: 0725342110 | 0712839006 | 0812077337</p>
        </div>

        <hr class="divider">

        <div class="meta-row">
            <span>Invoice No</span>
            <b>#<?= htmlspecialchars($invoice['invoice_no']); ?></b>
        </div>

        <div class="meta-row">
            <span>Date</span>
            <span><?= date("Y-m-d H:i", strtotime($invoice['created_at'])); ?></span>
        </div>

        <?php if($customer){ ?>
        <div class="meta-row">
            <span>Customer</span>
            <span><?= htmlspecialchars($customer); ?></span>
        </div>
        <?php } ?>

        <hr class="divider">

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Disc%</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $row){

                    $qty      = (float)$row['quantity'];
                    $price    = (float)$row['selling_price'];
                    $discount = (float)($row['discount'] ?? 0);

                    $lineSub   = $qty * $price;
                    $lineDisc  = $lineSub * ($discount / 100);
                    $lineTotal = $lineSub - $lineDisc;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name']); ?></td>
                    <td><?= (int)$qty; ?></td>
                    <td><?= number_format($price,2); ?></td>
                    <td><?= $discount > 0 ? number_format($discount,1).'%' : '-'; ?></td>
                    <td><?= number_format($lineTotal,2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="summary-box">

            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rs. <?= number_format($subtotal,2); ?></span>
            </div>

            <?php if($totalDiscount > 0){ ?>
            <div class="summary-row discount-row">
                <span>Discount</span>
                <span>- Rs. <?= number_format($totalDiscount,2); ?></span>
            </div>
            <?php } ?>

            <div class="summary-row grand-total">
                <span>Grand Total</span>
                <span>Rs. <?= number_format($grandTotal,2); ?></span>
            </div>

        </div>

        <?php if($paymentMethod || $paidAmount !== null){ ?>
        <hr class="divider">
        <div class="payment-box">

            <?php if($paymentMethod){ ?>
            <div class="summary-row">
                <span>Payment Method</span>
                <span><?= htmlspecialchars($paymentMethod); ?></span>
            </div>
            <?php } ?>

            <?php if($paidAmount !== null){ ?>
            <div class="summary-row">
                <span>Paid</span>
                <span>Rs. <?= number_format($paidAmount,2); ?></span>
            </div>
            <?php } ?>

            <?php if($balance !== null){ ?>
            <div class="summary-row">
                <span>Balance</span>
                <span>Rs. <?= number_format($balance,2); ?></span>
            </div>
            <?php } ?>

        </div>
        <?php } ?>

        <div class="thank-you">
            <div>Thank You! Come Again </div>
            <small>This is a computer generated invoice</small>
        </div>

        <div class="actions">

            <button class="btn-print" onclick="window.print()">
                🖨 Print
            </button>

            <button class="btn-back" onclick="window.location.href='index.php'">
                ⬅ Back
            </button>

        </div>

    </div>

</body>
</html>