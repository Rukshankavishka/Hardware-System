<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



if($_SERVER["REQUEST_METHOD"] == "POST"){


    $name = $_POST['name'];

    $nic = $_POST['nic'];

    $phone = $_POST['phone'];

    $address = $_POST['address'];

    $position = $_POST['position'];

    $join_date = $_POST['join_date'];

    $basic_salary = $_POST['basic_salary'];

    $status = $_POST['status'];




    // Generate Employee Code

    $stmt = $pdo->query("
        SELECT employee_id 
        FROM employees
        ORDER BY employee_id DESC
        LIMIT 1
    ");


    $last = $stmt->fetch(PDO::FETCH_ASSOC);



    if($last){

        $next = $last['employee_id'] + 1;

    }else{

        $next = 1;

    }



    $employee_code = "EMP" . str_pad($next,4,"0",STR_PAD_LEFT);





    // Insert Employee


    $sql = "

    INSERT INTO employees

    (
        employee_code,
        name,
        nic,
        phone,
        address,
        position,
        join_date,
        basic_salary,
        status
    )


    VALUES

    (
        :employee_code,
        :name,
        :nic,
        :phone,
        :address,
        :position,
        :join_date,
        :basic_salary,
        :status
    )

    ";



    $stmt = $pdo->prepare($sql);



    $stmt->execute([


        ":employee_code"=>$employee_code,

        ":name"=>$name,

        ":nic"=>$nic,

        ":phone"=>$phone,

        ":address"=>$address,

        ":position"=>$position,

        ":join_date"=>$join_date,

        ":basic_salary"=>$basic_salary,

        ":status"=>$status


    ]);



    header("Location:index.php");

    exit();


}

?>