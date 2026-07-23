<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



if($_SERVER["REQUEST_METHOD"] == "POST"){



    $supplier_name = $_POST['supplier_name'];

    $company_name = $_POST['company_name'];

    $phone = $_POST['phone'];

    $address = $_POST['address'];

    $br_no = $_POST['br_no'];




    // Generate Supplier Code

    $stmt = $pdo->query(
        "SELECT supplier_id 
         FROM suppliers 
         ORDER BY supplier_id DESC 
         LIMIT 1"
    );


    $last = $stmt->fetch(PDO::FETCH_ASSOC);



    if($last){

        $next = $last['supplier_id'] + 1;

    }
    else{

        $next = 1;

    }



    $supplier_code = "SUP" . str_pad($next,4,"0",STR_PAD_LEFT);





    // Insert Data


    $sql = "INSERT INTO suppliers

    (
        supplier_code,
        supplier_name,
        company_name,
        phone,
        address,
        br_no
    )

    VALUES

    (
        :supplier_code,
        :supplier_name,
        :company_name,
        :phone,
        :address,
        :br_no
    )";




    $stmt = $pdo->prepare($sql);



    $stmt->execute([


        ":supplier_code"=>$supplier_code,

        ":supplier_name"=>$supplier_name,

        ":company_name"=>$company_name,

        ":phone"=>$phone,

        ":address"=>$address,

        ":br_no"=>$br_no


    ]);





    header("Location:index.php");

    exit();



}


?>