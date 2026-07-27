<?php

$page = "accounts";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';

?>

<link rel="stylesheet" href="../../assets/css/accounts.css">


<div class="account-container">


    <div class="page-header">

        <h2>
            💰 Accounts Dashboard
        </h2>


        <button class="add-btn" onclick="openTransactionModal()">
            + Add Transaction
        </button>

    </div>



    <div class="account-summary">


        <div class="summary-box">

            <h4>
                Today's Income
            </h4>

            <h2>
                Rs. 0.00
            </h2>

        </div>



        <div class="summary-box">

            <h4>
                Today's Expense
            </h4>

            <h2>
                Rs. 0.00
            </h2>

        </div>



        <div class="summary-box">

            <h4>
                Balance
            </h4>

            <h2>
                Rs. 0.00
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

                    <td colspan="6">
                        No Transactions Found
                    </td>

                </tr>

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