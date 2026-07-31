<?php

$page="settings";


require_once "../../config/database.php";

$database = new Database();
$pdo=$database->connect();



$id=$_GET['id'];


// get user

$stmt=$pdo->prepare("
SELECT *
FROM users
WHERE id=?
");

$stmt->execute([$id]);

$user=$stmt->fetch(PDO::FETCH_ASSOC);



if(isset($_POST['update'])){


$name=$_POST['full_name'];
$username=$_POST['username'];
$role=$_POST['role'];
$status=$_POST['status'];



if(!empty($_POST['password'])){


$password=password_hash($_POST['password'],PASSWORD_DEFAULT);


$sql="
UPDATE users SET
full_name=?,
username=?,
password=?,
role=?,
status=?
WHERE id=?
";


$data=[
$name,
$username,
$password,
$role,
$status,
$id
];


}else{


$sql="
UPDATE users SET
full_name=?,
username=?,
role=?,
status=?
WHERE id=?
";


$data=[
$name,
$username,
$role,
$status,
$id
];


}



$stmt=$pdo->prepare($sql);

$stmt->execute($data);



echo "
<script>
alert('User Updated');
window.location='users.php';
</script>
";


}



require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>


<link rel="stylesheet" href="../../assets/css/settings.css">


<div class="edit-container">

<div class="edit-card">


<h2>
<i class="fa-solid fa-user-pen"></i>
Edit User
</h2>


<form method="POST">


<label>Full Name</label>

<input type="text"
name="full_name"
value="<?= $user['full_name']; ?>">



<label>Username</label>

<input type="text"
name="username"
value="<?= $user['username']; ?>">



<label>New Password</label>

<input type="password"
name="password"
placeholder="Leave empty to keep old password">



<label>Role</label>

<select name="role">

<option <?= $user['role']=="Admin"?"selected":""; ?>>
Admin
</option>


<option <?= $user['role']=="Cashier"?"selected":""; ?>>
Cashier
</option>

</select>



<label>Status</label>

<select name="status">


<option value="active"
<?= $user['status']=="active"?"selected":""; ?>>
Active
</option>


<option value="inactive"
<?= $user['status']=="inactive"?"selected":""; ?>>
Inactive
</option>


</select>



<div class="edit-buttons">


<a href="users.php" class="back-btn">
Back
</a>


<button name="update" class="save-btn">
Update User
</button>


</div>


</form>


</div>

</div>