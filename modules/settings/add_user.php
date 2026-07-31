<?php

$page = "settings";

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


if(isset($_POST['save'])){


    $full_name = $_POST['full_name'];
    $username  = $_POST['username'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role      = $_POST['role'];
    $status    = $_POST['status'];



    try{


        $stmt = $pdo->prepare("
            INSERT INTO users
            (
                full_name,
                username,
                password,
                role,
                status
            )

            VALUES
            (
                :full_name,
                :username,
                :password,
                :role,
                :status
            )
        ");



        $stmt->execute([

            ":full_name"=>$full_name,
            ":username"=>$username,
            ":password"=>$password,
            ":role"=>$role,
            ":status"=>$status

        ]);



        echo "<script>
        alert('User Added Successfully');
        window.location='users.php';
        </script>";


    }catch(Exception $e){

        echo $e->getMessage();

    }


}



require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>


<link rel="stylesheet" href="../../assets/css/settings.css">


<div class="form-container">


<div class="form-card">


<h2>
<i class="fa-solid fa-user-plus"></i>
Add New User
</h2>



<form method="POST">


<div class="input-group">

<label>Full Name</label>

<input type="text" 
name="full_name"
required>

</div>



<div class="input-group">

<label>Username</label>

<input type="text"
name="username"
required>

</div>




<div class="input-group">

<label>Password</label>

<input type="password"
name="password"
required>

</div>



<div class="input-group">

<label>Role</label>

<select name="role">


<option value="Admin">
Admin
</option>


<option value="Cashier">
Cashier
</option>


</select>

</div>



<div class="input-group">

<label>Status</label>

<select name="status">


<option value="active">
Active
</option>


<option value="inactive">
Inactive
</option>


</select>


</div>



<div class="form-buttons">

    <a href="users.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
        Back
    </a>


    <button name="save" class="save-btn">

        <i class="fa-solid fa-save"></i>
        Save User

    </button>

</div>


</form>


</div>


</div>



<?php

require_once "../../includes/footer.php";

?>