<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


if(isset($_GET['id'])){


    $id = $_GET['id'];


    $query = "DELETE FROM products WHERE product_id = :id";


    $stmt = $pdo->prepare($query);


    $stmt->execute([

        ':id'=>$id

    ]);


}


echo "<script>

alert('Product Deleted Successfully');

window.location='index.php';

</script>";

exit;


?>