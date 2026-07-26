<?php

require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();


if(isset($_GET['id'])){

    $id = $_GET['id'];


    $stmt = $pdo->prepare(
        "DELETE FROM suppliers 
         WHERE supplier_id = :id"
    );


    $stmt->execute([
        ":id"=>$id
    ]);

}


header("Location:index.php");
exit();

?>