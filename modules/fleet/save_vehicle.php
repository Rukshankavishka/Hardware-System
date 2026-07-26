<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



if($_SERVER["REQUEST_METHOD"] == "POST"){


    $vehicle_no = $_POST['vehicle_no'];

    $vehicle_type = $_POST['vehicle_type'];

    $model = $_POST['model'];

    $driver_name = $_POST['driver_name'];

    $driver_phone = $_POST['driver_phone'];

    $status = $_POST['status'];

    $last_service_date = $_POST['last_service_date'];

    $last_service_details = $_POST['last_service_details'];

    $last_service_cost = $_POST['last_service_cost'] ?: 0;

    $daily_fuel_liters = $_POST['daily_fuel_liters'] ?: 0;

    $daily_fuel_cost = $_POST['daily_fuel_cost'] ?: 0;

    $notes = $_POST['notes'];




    $sql = "

    INSERT INTO vehicles

    (

    vehicle_no,
    vehicle_type,
    model,
    driver_name,
    driver_phone,
    status,
    last_service_date,
    last_service_details,
    last_service_cost,
    daily_fuel_liters,
    daily_fuel_cost,
    notes

    )

    VALUES

    (

    :vehicle_no,
    :vehicle_type,
    :model,
    :driver_name,
    :driver_phone,
    :status,
    :last_service_date,
    :last_service_details,
    :last_service_cost,
    :daily_fuel_liters,
    :daily_fuel_cost,
    :notes

    )

    ";


    $stmt = $pdo->prepare($sql);



    $stmt->execute([


        ":vehicle_no"=>$vehicle_no,

        ":vehicle_type"=>$vehicle_type,

        ":model"=>$model,

        ":driver_name"=>$driver_name,

        ":driver_phone"=>$driver_phone,

        ":status"=>$status,

        ":last_service_date"=>$last_service_date,

        ":last_service_details"=>$last_service_details,

        ":last_service_cost"=>$last_service_cost,

        ":daily_fuel_liters"=>$daily_fuel_liters,

        ":daily_fuel_cost"=>$daily_fuel_cost,

        ":notes"=>$notes


    ]);



    header("Location:index.php");

    exit();


}

?>