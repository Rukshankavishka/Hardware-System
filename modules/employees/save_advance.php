<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



if($_SERVER["REQUEST_METHOD"] == "POST"){


    $employee_id = $_POST['employee_id'];

    $advance_date = $_POST['advance_date'];

    $today = date('Y-m-d');

    if($advance_date != $today){

        echo "<script>

        alert('Advance can only be added for today.');

        history.back();

        </script>";

        exit();

    }

    $amount = $_POST['amount'];

    $reason = $_POST['reason'];



    $sql = "

    INSERT INTO employee_advances

    (
        employee_id,
        advance_date,
        amount,
        reason
    )

    VALUES

    (
        :employee_id,
        :advance_date,
        :amount,
        :reason
    )

    ";



    $stmt = $pdo->prepare($sql);



    $stmt->execute([

        ":employee_id" => $employee_id,

        ":advance_date" => $advance_date,

        ":amount" => $amount,

        ":reason" => $reason

    ]);



    echo "

    <script>

    alert('Advance Added Successfully');

    window.location='view_employee.php?id=$employee_id';

    </script>

    ";



    exit();


}


?>