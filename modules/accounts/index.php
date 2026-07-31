<?php

require_once '../../includes/auth.php';

$page = "accounts";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


// Today's Date

$today = date('Y-m-d');


// Get Transactions

$stmt = $pdo->prepare("

SELECT *

FROM transactions

WHERE transaction_date = :date

ORDER BY transaction_id DESC

");


$stmt->execute([

":date"=>$today

]);


$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Total Income

$income = $pdo->prepare("

SELECT SUM(amount) AS total

FROM transactions

WHERE transaction_date = :date

AND type='IN'

");


$income->execute([

":date"=>$today

]);


$total_income = $income->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;



// Total Expense

$expense = $pdo->prepare("

SELECT SUM(amount) AS total

FROM transactions

WHERE transaction_date = :date

AND type='OUT'

");


$expense->execute([

":date"=>$today

]);


$total_expense = $expense->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;



// Balance

$balance = $total_income - $total_expense;

?>
<link rel="stylesheet" href="../../assets/css/accounts.css">


<div class="account-container">


    <div class="page-header">

        <h2>
            💰 Accounts Dashboard
        </h2>
        <br>

        <button class="add-btn" onclick="openTransactionModal()">
            + Add Transaction
        </button>

    </div>

    <br>

    <div class="account-summary">


        <div class="summary-box">

            <h4>
                Today's Income
            </h4>

            <h2>
                Rs. <?= number_format($total_income,2); ?>
            </h2>

        </div>



        <div class="summary-box">

            <h4>
                Today's Expense
            </h4>

            <h2>
                Rs. <?= number_format($total_expense,2); ?>
            </h2>

        </div>



        <div class="summary-box">

            <h4>
                Balance
            </h4>

            <h2>
                Rs. <?= number_format($balance,2); ?>
            </h2>

        </div>


    </div>


    <br>


    <div class="account-card">

        <h3>
            📄 Recent Transactions
        </h3>


        <table class="account-table">


            <thead>

                <tr>

                    <th>Date</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Payment</th>

                </tr>

            </thead>


            <tbody>

                <tr>

                    <?php foreach($transactions as $row){ ?>


                    <td>
                    <?= date("d-m-Y",strtotime($row['transaction_date'])); ?>
                    </td>


                    <td>

                    <?php

                    if($row['type']=="IN"){

                    echo "<span class='present'>IN</span>";

                    }else{

                    echo "<span class='absent'>OUT</span>";

                    }

                    ?>

                    </td>


                    <td>
                    <?= $row['category']; ?>
                    </td>


                    <td>
                    <?= $row['description']; ?>
                    </td>


                    <td>
                    Rs. <?= number_format($row['amount'],2); ?>
                    </td>


                    <td>
                    <?= $row['payment_method']; ?>
                    </td>


                    </tr>


                    <?php } ?>


            </tbody>


        </table>


    </div>


</div>

<div id="transactionModal" class="modal">


<div class="modal-box">


<span class="close" onclick="closeTransactionModal()">
    &times;
</span>


<h2>
    💰 Add Transaction
</h2>


<form action="save_transaction.php" method="POST">


<label>Date</label>

<input type="date"
name="transaction_date"
value="<?= date('Y-m-d'); ?>"
required>


<label>Type</label>

<select name="type" required>

<option value="IN">
Money IN
</option>

<option value="OUT">
Money OUT
</option>

</select>



<label>Category</label>

<select name="category" required>

<option value="Sale">
Sale
</option>

<option value="Customer Payment">
Customer Payment
</option>

<option value="Owner Investment">
Owner Investment
</option>

<option value="Supplier Payment">
Supplier Payment
</option>

<option value="Employee Advance">
Employee Advance
</option>

<option value="Expense">
Other Expense
</option>

</select>



<label>Description</label>

<textarea name="description"></textarea>



<label>Amount</label>

<input type="number"
name="amount"
step="0.01"
required>



<label>Payment Method</label>

<select name="payment_method">

<option value="Cash">
Cash
</option>

<option value="Card">
Card
</option>

<option value="Bank">
Bank
</option>

</select>



<button type="submit" class="save-btn">
Save Transaction
</button>


</form>


</div>

</div>

<script>

    function openTransactionModal(){

        document.getElementById("transactionModal").style.display="flex";

    }


    function closeTransactionModal(){

        document.getElementById("transactionModal").style.display="none";

    }

</script>
<?php require_once '../../includes/footer.php'; ?>