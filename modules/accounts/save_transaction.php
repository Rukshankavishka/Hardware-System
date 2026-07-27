<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



if($_SERVER["REQUEST_METHOD"]=="POST"){


    $sql = "

    INSERT INTO transactions

    (
    transaction_date,
    type,
    category,
    description,
    amount,
    payment_method
    )

    VALUES

    (
    :transaction_date,
    :type,
    :category,
    :description,
    :amount,
    :payment_method
    )

    ";


    $stmt = $pdo->prepare($sql);


    $stmt->execute([

        ":transaction_date"=>$_POST['transaction_date'],

        ":type"=>$_POST['type'],

        ":category"=>$_POST['category'],

        ":description"=>$_POST['description'],

        ":amount"=>$_POST['amount'],

        ":payment_method"=>$_POST['payment_method']

    ]);


    echo "
    <script>
    alert('Transaction Saved Successfully');
    window.location='index.php';
    </script>
    ";


}

?>