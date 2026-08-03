<?php

require_once '../../includes/auth.php';

$page = "billing";

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();


if(!isset($_SESSION['invoice_no'])){

    header("Location: start_bill.php");
    exit();

}
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';



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

        <div class="bill-container" id="billContainer">


        <div class="shop-header">

            <h1>
                <img src="../../assets/images/logo.png" class="shop-logo">
                THISARU 
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
                Invoice No :
                <b id="invoiceNo"><?= $invoice_no ?></b>
            </div>


            <div>

            Date :
            <?= date("Y-m-d H:i") ?>

            </div>

            <div>
            Customer :

            <input type="text"
            class="customer-input">

            </div>

        </div>


        <hr>



        <table class="bill-table">

        <thead>

        <tr>

        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Discount</th>
        <th>Amount</th>
        <th>Action</th>


        </tr>

        </thead>



        <tbody>

            <?php foreach($cart_items as $row){ ?>

            <tr>

                <!-- Product Name -->
                <td>
                    <?= $row['product_name']; ?>
                </td>

                <!-- Quantity -->
                <td>
                    <input type="number"
                        class="form-control qty"
                        value="<?= $row['quantity']; ?>"
                        min="1"
                        style="width:80px;">
                </td>

                <!-- Price -->
                <td>
                    <input type="number"
                        class="form-control price"
                        value="<?= $row['selling_price']; ?>"
                        step="0.01"
                        style="width:120px;">
                </td>

                <!-- Discount -->
                <td>
                    <input type="number"
                        class="form-control discount"
                        value="0"
                        min="0"
                        max="100"
                        style="width:100px;">
                </td>

                <!-- Row Total -->
                <td>
                    Rs. <span class="line-total"><?= $row['total']; ?></span>
                </td>

                <!-- Remove -->
               <td>
                    <button type="button"
                            class="btn btn-danger"
                            onclick="removeItem(<?= $row['cart_id']; ?>)">
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

        <h3>

        Grand Total :

        Rs.

        <span id="grandTotal">

            <?= number_format($grand_total,2); ?>
            0.00
        </span>

        </h3>


        </div>

            <div class="summary">

                <h5>Payment</h5>

                <label>
                    <input type="radio" name="payment_method" value="Cash" checked>
                    Cash
                </label>

                <label class="ms-3">
                    <input type="radio" name="payment_method" value="Card">
                    Card
                </label>


                <div class="mt-3">

                    <label>Paid Amount Rs.</label>

                    <input type="number"
                        id="paidAmount"
                        class="form-control"
                        placeholder="Enter amount">

                </div>


                <div class="mt-3">

                    <h5>
                        Balance :
                        Rs. <span id="balance">0.00</span>
                    </h5>

                </div>

            </div>

            <div class="bill-actions">
                        <button type="button" onclick="markComplete()" class="billcomplete no-print">
                            ✅ Complete
                        </button>
 
                        <button type="button" onclick="markNotComplete()" class="bullnot-complete no-print">
                            ❌ Not Complete
                        </button>
                        <br>
                        <button type="button" onclick="completeBill()" class="no-print">
                            🖨 Print Bill
                        </button>
                        
            </div>

            <div align="center">
                <br>
                <br>
                <br>
                <br>
                <h3>
                    Thank You! & Come again

                </h3>

            </div>


        </div>

       <script>

            function calculateRow(row){

                let qty = parseFloat(row.querySelector('.qty').value) || 0;
                let price = parseFloat(row.querySelector('.price').value) || 0;
                let discount = parseFloat(row.querySelector('.discount').value) || 0;

                let subtotal = qty * price;
                let discountAmount = subtotal * (discount / 100);
                let total = subtotal - discountAmount;

                row.querySelector('.line-total').innerHTML = total.toFixed(2);

                calculateBillTotal();
                calculateRow(newRow);
            }

            function calculateBillTotal(){

                let subtotal = 0;

                document.querySelectorAll('.line-total').forEach(function(cell){
                    subtotal += parseFloat(cell.innerHTML) || 0;
                });

                document.getElementById('grandTotal').innerHTML = subtotal.toFixed(2);
            }

            // Inputs වල event listeners
            document.querySelectorAll('.qty, .price, .discount').forEach(function(input){

                input.addEventListener('keyup', function(){
                    calculateRow(this.closest('tr'));
                });

                input.addEventListener('change', function(){
                    calculateRow(this.closest('tr'));
                });

            });

            calculateBillTotal();

            function completeBill(){

                let paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

                fetch("save_bill.php", {
                    method: "POST",
                    headers: {"Content-Type":"application/x-www-form-urlencoded"},
                    body: "payment_method=" + encodeURIComponent(paymentMethod)
                })
                .then(response => response.json())
                .then(data => {

                    if(data.status === "success"){

                        // 1. Print current bill (as is, with old invoice number)
                        window.print();

                        // 2. After print dialog closes, reset UI for the NEW invoice
                        document.getElementById("invoiceNo").innerText = data.new_invoice_no;

                        document.querySelector(".bill-table tbody").innerHTML = "";

                        document.getElementById("grandTotal").innerText = "0.00";
                        document.getElementById("paidAmount").value = "";
                        document.getElementById("balance").innerText = "0.00";
                        document.querySelector(".customer-input").value = "";

                    }else{
                        alert(data.message);
                    }

                })
                .catch(err => {
                    console.error(err);
                    alert("Network error, please try again.");
                });
            }

            document.getElementById("paidAmount")
                .addEventListener("input", function(){

                    let grandTotal = 
                    parseFloat(document.getElementById("grandTotal").innerText) || 0;


                    let paid = 
                    parseFloat(this.value) || 0;


                    let balance = paid - grandTotal;


                    document.getElementById("balance").innerText =
                    balance.toFixed(2);

                });

                function removeItem(cart_id){

                    if(confirm("Remove this product from bill?")){

                        window.location.href = "remove_cart.php?id=" + cart_id;

                    }

                }

            </script>
    </body>
</html>
<?php require_once '../../includes/footer.php'; ?>

