<?php

require_once '../../config/database.php';


$database = new Database();
$conn = $database->connect();



$customers =  $conn->query(
    "SELECT * FROM customers 
     WHERE status='completed'"
)->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>
<html>

<head>

<title>Completed Customers</title>

<link rel="stylesheet" href="../../assets/css/customers.css">

</head>


<body>


<div class="customer-page">


<div class="page-header">

<h2>
Completed Customers History
</h2>


<a href="index.php">
<button class="add-btn">
Back Customers
</button>
</a>


</div>



<div class="customer-card">


<table>


<thead>

<tr>

<th>Name</th>

<th>Phone</th>

<th>Total Credit</th>

<th>Total Paid</th>

<th>Completed Date</th>

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
<?= $row['last_payment_date']; ?>
</td>


<td>

<span class="active">
Completed
</span>

</td>

<td>

    <button class="delete"
    onclick="deleteCustomer(<?= $row['id']; ?>)">

    Delete

    </button>

</td>


</tr>


<?php } ?>


</tbody>


</table>


</div>


</div>

<script>

    function deleteCustomer(id){

    if(confirm("Are you sure you want to delete this customer?")){

        window.location.href="delete_customer.php?id="+id;

    }

}

</script>
</body>

</html>