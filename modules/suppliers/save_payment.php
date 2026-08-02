<?php

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

$purchase_id    = $_POST['purchase_id'] ?? null;
$payment_amount = $_POST['payment_amount'] ?? null;
$payment_method = $_POST['payment_method'] ?? 'Cash';
$cheque_no      = $_POST['cheque_no'] ?? null;
$cheque_bank    = $_POST['cheque_bank'] ?? null;
$cheque_date    = $_POST['cheque_date'] ?? null;

if(!$purchase_id || !$payment_amount || $payment_amount <= 0){
    die("Invalid payment data");
}

// Cheque select කළොත් cheque details ටික compulsory කරන්න
if($payment_method === 'Cheque'){
    if(!$cheque_no || !$cheque_bank || !$cheque_date){
        die("Cheque details are required for cheque payments");
    }
} else {
    // Cash නම් cheque fields null කරලා දාන්න (junk data DB එකට යන්නේ නැති වෙන්න)
    $cheque_no = null;
    $cheque_bank = null;
    $cheque_date = null;
}

try{

    $pdo->beginTransaction();

    /* Current Purchase Details */

    $stmt = $pdo->prepare("
        SELECT paid_amount, total, supplier_id
        FROM purchase_history
        WHERE purchase_id = :id
    ");

    $stmt->execute([
        ":id"=>$purchase_id
    ]);

    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$purchase){
        $pdo->rollBack();
        die("Purchase not found");
    }

    $new_paid = $purchase['paid_amount'] + $payment_amount;
    $new_balance = $purchase['total'] - $new_paid;

    /* Update Payment */

    $stmt = $pdo->prepare("
        UPDATE purchase_history
        SET 
            paid_amount = :paid,
            balance = :balance
        WHERE purchase_id = :id
    ");

    $stmt->execute([
        ":paid"=>$new_paid,
        ":balance"=>$new_balance,
        ":id"=>$purchase_id
    ]);

    /* Save Transaction Record (Money going OUT to supplier) */

    $stmt = $pdo->prepare("
        INSERT INTO transactions
        (
            transaction_date,
            type,
            category,
            reference_type,
            reference_id,
            description,
            amount,
            payment_method,
            cheque_no,
            cheque_bank,
            cheque_date
        )
        VALUES
        (
            CURDATE(),
            'OUT',
            'Purchase Payment',
            'Purchase',
            :reference_id,
            'Supplier Payment',
            :amount,
            :payment_method,
            :cheque_no,
            :cheque_bank,
            :cheque_date
        )
    ");

    $stmt->execute([
        ":reference_id"=>$purchase_id,
        ":amount"=>$payment_amount,
        ":payment_method"=>$payment_method,
        ":cheque_no"=>$cheque_no,
        ":cheque_bank"=>$cheque_bank,
        ":cheque_date"=>$cheque_date
    ]);

    $pdo->commit();

    header("Location:supplier_details.php?id=".$purchase['supplier_id']);
    exit();

}catch(Exception $e){

    $pdo->rollBack();
    error_log($e->getMessage());
    die("Payment failed. Please try again.");

}

?>