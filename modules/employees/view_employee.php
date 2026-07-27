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

SELECT *

FROM attendance

WHERE employee_id = :id

ORDER BY attendance_date DESC

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

$current_month = date('Y-m');


$totalAdvance = $pdo->prepare("

SELECT SUM(amount) AS total

FROM employee_advances

WHERE employee_id = :id

AND DATE_FORMAT(advance_date,'%Y-%m') = :month

AND status = 'Pending'

");


$totalAdvance->execute([

":id"=>$id,

":month"=>$current_month

]);


$monthlyAdvance = $totalAdvance->fetch(PDO::FETCH_ASSOC);


$monthly_total = $monthlyAdvance['total'] ?? 0;


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

    <br>

    <div class="employee-card">

        <div class="page-header">
            <h3>
                📅 Attendance History
            </h3>
            <a href="attendance.php?employee_id=<?= $employee['employee_id']; ?>"class="add-btn">
                + Add Attendance
            </a>
        </div>

        <table class="employee-table">

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if(count($attendance_data)>0){
                foreach($attendance_data as $row){

                ?>
            <tr>
                <td>
                    <?= date("d-m-Y",strtotime($row['attendance_date'])); ?>
                </td>
                <td>
                    <?php
                    if($row['status']=="Present"){
                    echo "<span class='present'>✅ Present</span>";
                    }
                    elseif($row['status']=="Absent"){
                    echo "<span class='absent'>❌ Absent</span>";
                    }
                    elseif($row['status']=="Leave"){
                    echo "<span class='leave'>🟢 Leave</span>";
                    }
                    else{
                    echo "<span class='halfday'>🟡 Half Day</span>";
                    }

                    ?>
                </td>

            </tr>
            <?php
            }
            }else{
            ?>

            <tr>

            <td colspan="2">

            No Attendance Records Found

            </td>

            </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>

     <br>

    <div class="employee-card">
        
        <div class="page-header">
            <h3>
                💰 Advance History
            </h3>

            <button class="add-btn" onclick="openAdvanceModal()">
                + Add Advance
            </button>
        </div>
        

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

        <p class="advance-total">

            Current Month Advance :

            <b>
            Rs. <?= number_format($monthly_total,2); ?>
            </b>

        </p>

    </div>

     <br>

    <div class="employee-card">
        <div class="page-header">
            <h3>
                💵 Salary History
            </h3>
            <button class="add-btn" onclick="openSalaryModal(<?= $employee['employee_id']; ?>)">
                + Pay Salary
            </button>
        </div>

        <table class="employee-table">

            <tr>

            <th>Month</th>

            <th>Basic Salary</th>

            <th>Overtime</th>

            <th>Advance</th>

            <th>Net Salary</th>

            <th>Paid Date</th>

            </tr>
        <?php foreach($salaries as $row){ ?>
            <tr>
                <td>
                <?= $row['salary_month']; ?>
                </td>

                <td>Rs. <?= number_format($row['basic_salary'],2); ?></td>

                <td>Rs. <?= number_format($row['overtime'],2); ?></td>

                <td>Rs. <?= number_format($row['advance_deduct'],2); ?></td>

                <td><b>Rs. <?= number_format($row['final_salary'],2); ?></b></td>

                <td><?= date("d-m-Y",strtotime($row['paid_date'])); ?></td>
            </tr>

        <?php } ?>
        </table>
    </div>

</div>
<div id="advanceModal" class="modal">


<div class="modal-box">


<span class="close"
onclick="closeAdvanceModal()">

&times;

</span>


<h2>
💰 Add Advance
</h2>



<form action="save_advance.php" method="POST">


<input type="hidden"
name="employee_id"
value="<?= $employee['employee_id']; ?>">



<label>
Date
</label>

<input type="date" name="advance_date" value="<?= date('Y-m-d'); ?>" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>" required>

<label>
Amount
</label>


<input type="number"
name="amount"
step="0.01"
required>



<label>
Reason
</label>


<textarea name="reason"></textarea>



<button type="submit"
class="save-btn">

Save Advance

</button>



</form>



</div>

</div>

<!-- Salary Modal -->

<div id="salaryModal" class="modal">


<div class="modal-box">

    <span class="close" onclick="closeSalaryModal()">
        &times;
    </span>

<h2>
💵 Pay Salary
</h2>



<form action="save_salary.php" method="POST">


<input type="hidden"
name="employee_id"
value="<?= $employee['employee_id']; ?>">



<label>
Salary Month
</label>

<input type="month"
name="salary_month"
value="<?= date('Y-m'); ?>"
required>



<label>
Basic Salary
</label>

<input type="number"
id="basic_salary"
name="basic_salary"
value="<?= $employee['basic_salary']; ?>"
readonly>



<label>
Overtime
</label>

<input type="number"
id="overtime"
name="overtime"
step="0.01"
oninput="calculateSalary()">


<label>
Advance Deduct
</label>

<input type="number"
id="advance_deduct"
name="advance_deduct"
step="0.01"
readonly>

<label>
Final Salary
</label>

<input type="number"
id="final_salary"
name="final_salary"
readonly>



<label>
Paid Date
</label>

<input type="date"
name="paid_date"
value="<?= date('Y-m-d'); ?>"
required>



<button type="submit"
class="save-btn">

Save Salary

</button>


</form>


</div>

</div>
    <script>

        function openAdvanceModal(){

        document.getElementById("advanceModal").style.display="flex";

        }


        function closeAdvanceModal(){

        document.getElementById("advanceModal").style.display="none";

        }

       function openSalaryModal(employee_id){

            document.getElementById("salaryModal").style.display="flex";


            fetch("get_pending_advance.php?employee_id=" + employee_id)

            .then(response => response.json())

            .then(data => {

                document.getElementById("advance_deduct").value = data.total;

                calculateSalary();

            });


        }


        function closeSalaryModal(){

            document.getElementById("salaryModal").style.display="none";

        }

    </script>


<?php require_once '../../includes/footer.php'; ?>