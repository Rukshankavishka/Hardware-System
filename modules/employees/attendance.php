<?php

$page = "employees";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();
$selected_employee = $_GET['employee_id'] ?? '';


// Get Employees

$stmt = $pdo->query("
SELECT employee_id, employee_code, name
FROM employees
WHERE status='Active'
ORDER BY name ASC
");


$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);



if($_SERVER["REQUEST_METHOD"]=="POST"){


    $employee_id = $_POST['employee_id'];

    $attendance_date = $_POST['attendance_date'];

    $status = $_POST['status'];



    $sql = "

    INSERT INTO attendance

    (

    employee_id,
    attendance_date,
    status

    )

    VALUES

    (

    :employee_id,
    :attendance_date,
    :status

    )

    ";



    $stmt = $pdo->prepare($sql);



    $stmt->execute([

        ":employee_id"=>$employee_id,

        ":attendance_date"=>$attendance_date,

        ":status"=>$status

    ]);



    echo "<script>
    alert('Attendance Saved Successfully');
    window.location='attendance.php';
    </script>";

}


?>


<link rel="stylesheet" href="../../assets/css/employees.css">


<div class="employee-container">


    <div class="page-header">
        <h2>📅 Add Attendance</h2>
        <a href="index.php" class="delete-btn">
            ← Back
        </a>
    </div>

    <div class="employee-card">

        <form method="POST">

            <label>Select Employee</label>

            <select name="employee_id" required>

                <option value="">-- Select Employee --</option>

                <?php foreach($employees as $emp){ ?>

                <option value="<?= $emp['employee_id']; ?>">

                    <?= $emp['employee_code']; ?> -<?= $emp['name']; ?>

                </option>

                <?php } ?>

            </select>

            <label>Attendance Date</label>

            <input type="date" name="attendance_date" value="<?= date('Y-m-d'); ?>" required>

            <label>Status</label>


            <select name="status" required>


                <option value="Present">Present</option>

                <option value="Absent">Absent</option>

                <option value="Leave">Leave</option>

                <option value="Half Day">Half Day</option>

            </select>

            <button type="submit" class="save-btn">
                Save Attendance
            </button>

        </form>

    </div>

</div>



<?php require_once '../../includes/footer.php'; ?>