<?php

require_once '../../includes/auth.php';
require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

// Make sure PDO throws real errors instead of failing silently
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    try{

        // Prevent duplicate attendance for the same employee + date
        $check = $pdo->prepare("
            SELECT attendance_id
            FROM attendance
            WHERE employee_id = :employee_id
            AND attendance_date = :attendance_date
        ");

        $check->execute([
            ":employee_id" => $employee_id,
            ":attendance_date" => $attendance_date
        ]);

        if($check->rowCount() > 0){
            echo "<script>
            alert('Attendance already added for this employee on this date.');
            window.location='view_employee.php?id=" . $employee_id . "';
            </script>";
            exit();
        }

        $sql = "
            INSERT INTO attendance
            (employee_id, attendance_date, status)
            VALUES
            (:employee_id, :attendance_date, :status)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":employee_id" => $employee_id,
            ":attendance_date" => $attendance_date,
            ":status" => $status
        ]);

        echo "<script>
        alert('Attendance Saved Successfully');
        window.location='view_employee.php?id=" . $employee_id . "';
        </script>";
        exit();

    } catch(PDOException $e){
        // Show the real database error instead of a fake success message
        die("Database Error: " . $e->getMessage());
    }
}

// If accessed without POST, just send back
header("Location: index.php");
exit();