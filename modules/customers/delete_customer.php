<?php

require_once '../../config/database.php';

$database = new Database();
$conn = $database->connect();

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM customers WHERE id=?");
    $stmt->execute([$id]);

    echo "<script>

        alert('Customer deleted successfully!');

        window.location='completed.php';

    </script>";

    exit;

}

?>