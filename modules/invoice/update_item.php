<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

$database = new Database();
$pdo = $database->connect();

if(!isset($_GET['invoice_no'])){
    die("Invoice Not Found");
}

$invoice_no = $_GET['invoice_no'];

if(isset($_POST['save'])){

    foreach($_POST['taken'] as $id => $taken){

        $taken = (int)$taken;

        $stmt = $pdo->prepare("
            SELECT quantity
            FROM invoice_items
            WHERE invoice_item_id = :id
        ");

        $stmt->execute([
            ":id"=>$id
        ]);

        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        $remaining = $item['quantity'] - $taken;

        if($remaining < 0){
            $remaining = 0;
        }

        $update = $pdo->prepare("
            UPDATE invoice_items
            SET taken_quantity = :taken,
                remaining_quantity = :remaining
            WHERE invoice_item_id = :id
        ");

        $update->execute([

            ":taken"=>$taken,
            ":remaining"=>$remaining,
            ":id"=>$id

        ]);

    }

    header("Location:view.php?invoice_no=".$invoice_no);
    exit();

}

$stmt = $pdo->prepare("
SELECT *
FROM invoice_items
WHERE invoice_no=:invoice_no
");

$stmt->execute([
":invoice_no"=>$invoice_no
]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';

?>

<div class="container-fluid">

<h3>Update Invoice</h3>

<form method="POST">

<table class="table table-bordered">

<thead>

<tr>

<th>Product</th>
<th>Qty</th>
<th>Taken Qty</th>
<th>Remaining</th>

</tr>

</thead>

<tbody>

<?php foreach($items as $row){ ?>

<tr>

<td><?= $row['product_name']; ?></td>

<td><?= $row['quantity']; ?></td>

<td>

<input
type="number"
class="form-control"
name="taken[<?= $row['invoice_item_id']; ?>]"
value="<?= $row['taken_quantity']; ?>"
min="0"
max="<?= $row['quantity']; ?>">

</td>

<td>

<?= $row['remaining_quantity']; ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<button
type="submit"
name="save"
class="btn btn-success">

Save Changes

</button>

<a
href="view.php?invoice_no=<?= $invoice_no; ?>"
class="btn btn-secondary">

Back

</a>

</form>

</div>

<?php require_once '../../includes/footer.php'; ?>