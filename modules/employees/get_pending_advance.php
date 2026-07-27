<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


$employee_id = $_GET['employee_id'];


$stmt = $pdo->prepare("

SELECT SUM(amount) AS total

FROM employee_advances

WHERE employee_id = :id

AND status = 'Pending'

");


$stmt->execute([

":id"=>$employee_id

]);


$result = $stmt->fetch(PDO::FETCH_ASSOC);


echo json_encode([

"total" => $result['total'] ?? 0

]);

?>