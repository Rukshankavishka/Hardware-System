<?php

$page="settings";

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


$stmt = $pdo->query("
SELECT * FROM shop_settings LIMIT 1
");

$shop = $stmt->fetch(PDO::FETCH_ASSOC);



if(isset($_POST['save'])){


$shop_name = $_POST['shop_name'];
$address   = $_POST['address'];
$phone     = $_POST['phone'];
$email     = $_POST['email'];



if($shop){


$update=$pdo->prepare("
UPDATE shop_settings
SET shop_name=?,
address=?,
phone=?,
email=?
WHERE id=?
");


$update->execute([

$shop_name,
$address,
$phone,
$email,
$shop['id']

]);


}else{


$insert=$pdo->prepare("
INSERT INTO shop_settings
(shop_name,address,phone,email)

VALUES(?,?,?,?)

");


$insert->execute([

$shop_name,
$address,
$phone,
$email

]);


}



echo "
<script>
alert('Shop Information Updated');
window.location='shop_info.php';
</script>
";


}



require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>


<link rel="stylesheet" href="../../assets/css/settings.css">


<div class="shop-container">


<div class="shop-card">


<h2>
<i class="fa-solid fa-store"></i>
Shop Information
</h2>



<form method="POST">


<label>Shop Name</label>

<input type="text" 
name="shop_name"
value="<?= $shop['shop_name'] ?? ''; ?>"
required>



<label>Address</label>

<textarea name="address"><?= $shop['address'] ?? ''; ?></textarea>



<label>Phone Number</label>

<input type="text"
name="phone"
value="<?= $shop['phone'] ?? ''; ?>">



<label>Email</label>

<input type="email"
name="email"
value="<?= $shop['email'] ?? ''; ?>">



<div class="buttons">


<a href="index.php" class="back-btn">
<i class="fa-solid fa-arrow-left"></i>
Back
</a>


<button name="save">

<i class="fa-solid fa-save"></i>
Save Changes

</button>


</div>


</form>


</div>

</div>


<?php

require_once "../../includes/footer.php";

?>