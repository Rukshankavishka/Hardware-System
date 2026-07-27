<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



if($_SERVER["REQUEST_METHOD"] == "POST"){


    $employee_id = $_POST['employee_id'];

    $salary_month = $_POST['salary_month'];

    $basic_salary = $_POST['basic_salary'];

    $overtime = $_POST['overtime'] ?? 0;

    $deductions = $_POST['deductions'] ?? 0;

    $advance_deduct = $_POST['advance_deduct'] ?? 0;

    $final_salary = $_POST['final_salary'];

    $paid_date = $_POST['paid_date'];



    // Save Salary

    $sql = "

    INSERT INTO salary_payments

    (

    employee_id,
    salary_month,
    basic_salary,
    overtime,
    deductions,
    advance_deduct,
    final_salary,
    paid_date

    )

    VALUES

    (

    :employee_id,
    :salary_month,
    :basic_salary,
    :overtime,
    :deductions,
    :advance_deduct,
    :final_salary,
    :paid_date

    )

    ";



    $stmt = $pdo->prepare($sql);



    $stmt->execute([


    ":employee_id"=>$employee_id,

    ":salary_month"=>$salary_month,

    ":basic_salary"=>$basic_salary,

    ":overtime"=>$overtime,

    ":deductions"=>$deductions,

    ":advance_deduct"=>$advance_deduct,

    ":final_salary"=>$final_salary,

    ":paid_date"=>$paid_date


    ]);





    // Mark Advance as Paid

    if($advance_deduct > 0){


        $update = $pdo->prepare("

        UPDATE employee_advances

        SET status='Paid'

        WHERE employee_id = :employee_id

        AND status='Pending'

        ");


        $update->execute([

        ":employee_id"=>$employee_id

        ]);


    }




    echo "

    <script>

    alert('Salary Saved Successfully');

    window.location='view_employee.php?id=$employee_id';

    </script>

    ";



    exit();


}

?>