<?php

require_once '../../config/database.php';


$database = new Database();
$conn = $database->connect();


if(isset($_POST['save'])){


    $name = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $credit = $_POST['credit_amount'];
    $date = $_POST['purchase_date'];


    $balance = $credit;


    $sql = "INSERT INTO customers
            (customer_name, phone, credit_amount, paid_amount, balance, purchase_date)
            VALUES (?,?,?,?,?,?)";


    $stmt = $conn->prepare($sql);


    $stmt->execute([
        $name,
        $phone,
        $credit,
        0,
        $balance,
        $date
    ]);


    header("Location: index.php");
    exit;

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Add Customer</title>

<link rel="stylesheet" href="../../assets/css/style.css">

</head>


<body>


<div class="customer-page">


<h2>Add Credit Customer</h2>


<div class="customer-card">


<form method="POST">


<label>
Customer Name
</label>

<input type="text"
       name="customer_name"
       class="form-control"
       required>


<br>


<label>
Phone Number
</label>

<input type="text"
       name="phone"
       class="form-control"
       required>


<br>


<label>
Credit Amount
</label>

<input type="number"
       name="credit_amount"
       class="form-control"
       required>


<br>


<label>
Purchase Date
</label>

<input type="date"
       name="purchase_date"
       class="form-control"
       required>


<br>


<button type="submit"
        name="save"
        class="add-btn">

Save Customer

</button>


</form>


</div>


</div>


</body>

</html>