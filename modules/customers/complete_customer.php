<?php

require_once '../../config/database.php';


$database = new Database();
$conn = $database->connect();


if(isset($_GET['id'])){


    $id = $_GET['id'];


    $sql = "UPDATE customers 
            SET status='completed'
            WHERE id=?";


    $stmt = $conn->prepare($sql);

    $stmt->execute([$id]);


}


header("Location:index.php");
exit;

?>