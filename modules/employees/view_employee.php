<?php

$page = "employees";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



$id = $_GET['id'];


// Employee Details

$stmt = $pdo->prepare("
SELECT *
FROM employees
WHERE employee_id = :id
");


$stmt->execute([
":id"=>$id
]);


$employee = $stmt->fetch(PDO::FETCH_ASSOC);




// Attendance Summary

$attendance = $pdo->prepare("

SELECT 
status,
COUNT(*) as total

FROM attendance

WHERE employee_id = :id

GROUP BY status

");


$attendance->execute([
":id"=>$id
]);


$attendance_data = $attendance->fetchAll(PDO::FETCH_ASSOC);




// Advance History

$advance = $pdo->prepare("

SELECT *
FROM employee_advances

WHERE employee_id = :id

ORDER BY advance_id DESC

");


$advance->execute([
":id"=>$id
]);


$advances = $advance->fetchAll(PDO::FETCH_ASSOC);




// Salary History

$salary = $pdo->prepare("

SELECT *
FROM salary_payments

WHERE employee_id = :id

ORDER BY salary_id DESC

");


$salary->execute([
":id"=>$id
]);


$salaries = $salary->fetchAll(PDO::FETCH_ASSOC);


?>


<link rel="stylesheet" href="../../assets/css/employees.css">


<div class="employee-container">


    <div class="page-header">

        <h2>👨‍💼 Employee Details</h2>

        <a href="index.php" class="delete-btn"> ← Back</a>

    </div>

    <div class="employee-card">

        <h3>Personal Details</h3>

        <p><b>Employee Code :</b><?= $employee['employee_code']; ?></p>

        <p><b>Name :</b><?= $employee['name']; ?></p>

        <p><b>NIC :</b><?= $employee['nic']; ?></p>

        <p><b>Phone :</b><?= $employee['phone']; ?></p>

        <p><b>Position :</b><?= $employee['position']; ?></p>

        <p><b>Basic Salary :</b> Rs. <?= number_format($employee['basic_salary'],2); ?></p>

    </div>

    <div class="employee-card">


        <h3>📅 Attendance Summary</h3>
        <br>

        <a href="attendance.php?employee_id=<?= $employee['employee_id']; ?>" class="add-btn">
            + Add Attendance
        </a>
        
        <?php foreach($attendance_data as $row){ ?>

        <p><?= $row['status']; ?>
            :
            <b><?= $row['total']; ?>Days</b>
        </p>

        <?php } ?>
    </div>

    <div class="employee-card">
        <h3>
        💰 Advance History
        </h3>

        <table class="employee-table">

            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Reason</th>
            </tr>
                <?php foreach($advances as $row){ ?>
            <tr>
                <td><?= $row['advance_date']; ?></td>
                <td>Rs. <?= number_format($row['amount'],2); ?></td>
                <td><?= $row['reason']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="employee-card">
        <h3>
            💵 Salary History
        </h3>

        <table class="employee-table">
            <tr>
                <th>Month</th>
                <th>Final Salary</th>
                <th>Date</th>
            </tr>
        <?php foreach($salaries as $row){ ?>
            <tr>
                <td><?= $row['salary_month']; ?></td>
                <td> Rs. <?= number_format($row['final_salary'],2); ?></td>
                <td><?= $row['paid_date']; ?></td>
            </tr>

        <?php } ?>
        </table>
    </div>

</div>


<?php require_once '../../includes/footer.php'; ?>