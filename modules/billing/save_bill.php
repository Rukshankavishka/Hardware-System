<?php

session_start();
require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

header("Content-Type: application/json");

if(!isset($_SESSION['invoice_no'])){
    echo json_encode(["status"=>"error","message"=>"No Invoice"]);
    exit();
}

$invoice_no = $_SESSION['invoice_no'];
$status = $_POST['status'] ?? 'completed';

try{

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT SUM(total) AS total
        FROM temp_cart
        WHERE invoice_no = :invoice_no
    ");
    $stmt->execute([":invoice_no"=>$invoice_no]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    if(!$total){
        $pdo->rollBack();
        echo json_encode(["status"=>"error","message"=>"Cart is empty"]);
        exit();
    }

    $stmt = $pdo->prepare("
        UPDATE invoices
        SET total_amount = :total,
            status = :status
        WHERE invoice_no = :invoice_no
    ");

    $stmt->execute([
        ":total"      => $total,
        ":status"     => $status,
        ":invoice_no" => $invoice_no
    ]);

    $stmt = $pdo->prepare("
        INSERT INTO transactions
        (transaction_date, type, category, reference_type, reference_id, description, amount, payment_method)
        VALUES
        (CURDATE(), 'IN', 'Sales', 'Invoice', :invoice_no, 'Invoice Sale', :amount, :payment_method)
    ");
    $stmt->execute([
        ":invoice_no"=>$invoice_no,
        ":amount"=>$total,
        ":payment_method"=>$_POST['payment_method'] ?? 'Cash'
    ]);

    // ---- Get cart items (with everything we need: stock update, invoice_items, profit) ----
    $items = $pdo->prepare("
        SELECT
            product_id,
            product_name,
            quantity,
            selling_price,
            total
        FROM temp_cart
        WHERE invoice_no = :invoice_no
    ");
    $items->execute([":invoice_no"=>$invoice_no]);

    foreach($items as $row){

        // completed / pending logic
        if($status == "completed"){

            $taken_quantity = $row['quantity'];
            $remaining_quantity = 0;

        }else{

            $taken_quantity = 0;
            $remaining_quantity = $row['quantity'];

        }

        // 1) Reduce stock
        $update = $pdo->prepare("
            UPDATE products
            SET stock_qty = stock_qty - :qty
            WHERE product_id = :product_id
        ");
        $update->execute([
            ":qty"=>$row['quantity'],
            ":product_id"=>$row['product_id']
        ]);

        // 2) Buying Price එක products table එකෙන් ගන්න
        $buy = $pdo->prepare("
            SELECT buying_price
            FROM products
            WHERE product_id = :product_id
        ");
        $buy->execute([
            ":product_id"=>$row['product_id']
        ]);

        $product = $buy->fetch(PDO::FETCH_ASSOC);
        $buying_price = $product['buying_price'];

        $profit = ($row['selling_price'] - $buying_price) * $row['quantity'];

        // 3) invoice_items එකට save කරන්න
        $save = $pdo->prepare("
            INSERT INTO invoice_items
            (
                invoice_no,
                product_id,
                product_name,
                quantity,
                taken_quantity,
                remaining_quantity,
                buying_price,
                selling_price,
                line_total,
                profit
            )
            VALUES
            (
                :invoice_no,
                :product_id,
                :product_name,
                :quantity,
                :taken_quantity,
                :remaining_quantity,
                :buying_price,
                :selling_price,
                :line_total,
                :profit
            )
        ");

        $save->execute([
            ":invoice_no"         => $invoice_no,
            ":product_id"         => $row['product_id'],
            ":product_name"       => $row['product_name'],
            ":quantity"           => $row['quantity'],
            ":taken_quantity"     => $taken_quantity,
            ":remaining_quantity" => $remaining_quantity,
            ":buying_price"       => $buying_price,
            ":selling_price"      => $row['selling_price'],
            ":line_total"         => $row['total'],
            ":profit"             => $profit
        ]);
    }

    // Now that items are processed, clear the temp cart
    $stmt = $pdo->prepare("DELETE FROM temp_cart WHERE invoice_no=:invoice_no");
    $stmt->execute([":invoice_no"=>$invoice_no]);

    // ---- generate NEW invoice number right here ----
    $last = $pdo->query("SELECT invoice_no FROM invoices ORDER BY invoice_id DESC LIMIT 1")
                ->fetch(PDO::FETCH_ASSOC);

    $number = $last ? intval(str_replace("INV","",$last['invoice_no'])) + 1 : 1;
    $new_invoice_no = "INV" . str_pad($number,4,"0",STR_PAD_LEFT);

    $stmt = $pdo->prepare("
        INSERT INTO invoices (invoice_no, cashier_id, status)
        VALUES (:invoice_no, :cashier_id, 'pending')
    ");
    $stmt->execute([":invoice_no"=>$new_invoice_no, ":cashier_id"=>1]);

    $pdo->commit();

    $_SESSION['invoice_no'] = $new_invoice_no;

    echo json_encode([
        "status"        => "success",
        "invoice_no"    => $invoice_no,
        "new_invoice_no"=> $new_invoice_no
    ]);
    exit();

}catch(Exception $e){
    $pdo->rollBack();
    error_log($e->getMessage());
    echo json_encode(["status"=>"error","message"=>"Something went wrong. Try again."]);
    exit();
}