<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


$id = $_GET['id'];


$stmt = $pdo->prepare("
DELETE FROM users
WHERE id=?
");


$stmt->execute([$id]);


header("Location: users.php");

exit();

?>