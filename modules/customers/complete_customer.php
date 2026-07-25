<?php

require_once '../../config/database.php';

$database = new Database();
$conn = $database->connect();

if(isset($_GET['id'])){

    $id = $_GET['id'];

    // Customer balance බලන්න
    $stmt = $conn->prepare("SELECT balance FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Balance තියෙනවා නම් Complete කරන්න දෙන්න එපා
    if($customer['balance'] > 0){

        echo "
        <script>
            alert('⚠️ This customer has not completed the payment yet!');
            window.location='index.php';
        </script>
        ";
        exit;
    }

    // Balance = 0 නම් Complete කරන්න
    $update = $conn->prepare("UPDATE customers SET status='completed' WHERE id=?");
    $update->execute([$id]);

    echo "
    <script>
        alert('✅ Customer completed successfully!');
        window.location='completed.php';
    </script>
    ";
    exit;
}

?>