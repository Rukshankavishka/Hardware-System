<?php

require_once '../../config/database.php';

$database = new Database();
$conn = $database->connect();


if(isset($_GET['id'])){

    $cart_id = $_GET['id'];

    $sql = "DELETE FROM temp_cart WHERE cart_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$cart_id]);

}


header("Location: index.php");
exit;

?>