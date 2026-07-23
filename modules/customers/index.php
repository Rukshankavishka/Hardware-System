<?php

$page = "customers";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


require_once '../../config/database.php';

$database = new Database();
$conn = $database->connect();

if(isset($_POST['payment_save'])){


    $id = $_POST['customer_id'];

    $payment = $_POST['payment_amount'];


    $customer = $conn->prepare(
        "SELECT * FROM customers WHERE id=?"
    );

    $customer->execute([$id]);

    $data = $customer->fetch(PDO::FETCH_ASSOC);



    $newPaid = $data['paid_amount'] + $payment;

    $newBalance = $data['credit_amount'] - $newPaid;



    $update = $conn->prepare(
    "UPDATE customers SET 
    paid_amount=?,
    balance=?,
    last_payment_date=CURDATE()
    WHERE id=?"
    );


    $update->execute([
        $newPaid,
        $newBalance,
        $id
    ]);



    header("Location:index.php");
    exit;

}


if(isset($_POST['save'])){

    $name = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $credit = $_POST['credit_amount'];
    $date = $_POST['purchase_date'];


    $sql = "INSERT INTO customers
    (customer_name, phone, credit_amount, paid_amount, balance, purchase_date)
    VALUES (?,?,?,?,?,?)";


    $stmt = $conn->prepare($sql);


    $stmt->execute([
        $name,
        $phone,
        $credit,
        0,
        $credit,
        $date
    ]);


    header("Location: index.php");
    exit;

}


$customers = $conn->query(
    "SELECT * FROM customers WHERE status='active'"
)->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customers</title>

    <link rel="stylesheet" href="../../assets/css/customers.css">

</head>


<body>


<div class="customer-page">


    <div class="page-header">

        <div class="search-box">

            <input type="text"
                id="customerSearch"
                placeholder="Search Customer Name or Phone...">

        </div>


        <button class="add-btn" onclick="openCustomerModal()">
            + Add Customer
        </button>

        <a href="completed.php">

            <button class="add-btn">
                Completed Customers
            </button>

        </a>

    </div>



    <div class="customer-card">


        <table>


            <thead>

                <tr>

                    <th>Customer Name</th>

                    <th>Phone Number</th>

                    <th>Credit Amount</th>

                    <th>Paid Amount</th>

                    <th>Balance</th>

                    <th>Purchase Date</th>

                    <th>Last Payment Date</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>



            <tbody>


                <?php foreach($customers as $row){ ?>

                    <tr>

                        <td>
                            <?= $row['customer_name']; ?>
                        </td>


                        <td>
                            <?= $row['phone']; ?>
                        </td>


                        <td>
                            Rs. <?= number_format($row['credit_amount'],2); ?>
                        </td>


                        <td>
                            Rs. <?= number_format($row['paid_amount'],2); ?>
                        </td>


                        <td>
                            Rs. <?= number_format($row['balance'],2); ?>
                        </td>


                        <td>
                            <?= $row['purchase_date']; ?>
                        </td>


                        <td>
                            <?= $row['last_payment_date'] ?? '-'; ?>
                        </td>


                        <td>

                        <span class="active">
                            <?= $row['status']; ?>
                        </span>

                        </td>


                        <td>

                            <button class="edit">
                                Edit
                            </button>

                            <button class="payment" onclick="openPaymentModal( <?= $row['id']; ?>,<?= $row['balance']; ?>)">
                                    Payment
                            </button>

                            <button class="complete" onclick="completeCustomer(<?= $row['id']; ?>)">
                                Complete
                            </button>

                        </td>


                    </tr>


                    <?php } ?>


            </tbody>


        </table>
    </div>

    <div class="modal" id="paymentModal">

        <div class="modal-content">

            <span class="close" onclick="closePaymentModal()">
                &times;
            </span>


            <h3>Customer Payment</h3>


            <form method="POST">


                <input type="hidden" 
                    name="customer_id"
                    id="paymentCustomerId">


                <label>
                    Payment Amount
                </label>


                <input type="number"
                    name="payment_amount"
                    required>


                <button type="submit"
                        name="payment_save"
                        class="payment">

                    Save Payment

                </button>


            </form>


        </div>

    </div>

</div>

<div class="modal" id="customerModal">

    <div class="modal-content">

        <span class="close" onclick="closeCustomerModal()">
            &times;
        </span>


        <h3>Add Credit Customer</h3>


        <form method="POST">


            <label>
                Customer Name
            </label>

            <input type="text" 
                   name="customer_name"
                   required>


            <label>
                Phone Number
            </label>

            <input type="text"
                   name="phone"
                   required>


            <label>
                Credit Amount
            </label>

            <input type="number"
                   name="credit_amount"
                   required>


            <label>
                Purchase Date
            </label>

            <input type="date"
                   name="purchase_date"
                   required>


            <button type="submit"
                    name="save"
                    class="add-btn">

                Save Customer

            </button>


        </form>


    </div>

</div>
    <script>
            function openCustomerModal(){

                document.getElementById("customerModal")
                .style.display="flex";

            }


            function closeCustomerModal(){

                document.getElementById("customerModal")
                .style.display="none";

            }

            function openPaymentModal(id,balance){

                    document.getElementById("paymentCustomerId").value=id;

                    document.getElementById("paymentModal")
                    .style.display="flex";

                }


                function closePaymentModal(){

                    document.getElementById("paymentModal")
                    .style.display="none";

                }
            function completeCustomer(id){

                if(confirm("Complete this customer credit?")){

                    window.location.href =
                    "complete_customer.php?id=" + id;

                }

            }
            document.getElementById("customerSearch")
            .addEventListener("keyup", function(){

                let value = this.value.toLowerCase();


                let rows = document.querySelectorAll(
                    ".customer-card tbody tr"
                );


                rows.forEach(function(row){

                    let text = row.innerText.toLowerCase();


                    if(text.includes(value)){

                        row.style.display="";

                    }else{

                        row.style.display="none";

                    }

                });

            });

    </script>

</body>

</html>



<?php require_once '../../includes/footer.php'; ?>

