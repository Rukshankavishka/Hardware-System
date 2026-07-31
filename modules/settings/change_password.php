<?php

$page = "settings";

session_start();

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


$message = "";


if(isset($_POST['change'])){


    $current = $_POST['current_password'];
    $new      = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];


    // Example: Admin user id session එකෙන් ගන්නවා
    $user_id = $_SESSION['user_id'] ?? 1;


    $stmt = $pdo->prepare("
        SELECT password 
        FROM users
        WHERE id = ?
    ");

    $stmt->execute([$user_id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);



    if($user && password_verify($current,$user['password'])){


        if($new == $confirm){


            $hash = password_hash($new,PASSWORD_DEFAULT);


            $update = $pdo->prepare("
                UPDATE users
                SET password=?
                WHERE id=?
            ");


            $update->execute([
                $hash,
                $user_id
            ]);


            $message = "Password Changed Successfully";


        }else{

            $message = "New Password Not Match";

        }


    }else{

        $message = "Current Password Incorrect";

    }


}



require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>


<link rel="stylesheet" href="../../assets/css/settings.css">


<div class="password-container">


<div class="password-card">


<h2>
<i class="fa-solid fa-lock"></i>
Change Password
</h2>



<?php if($message): ?>

<div class="message">
<?= $message; ?>
</div>

<?php endif; ?>



<form method="POST">


<label>
Current Password
</label>

<input type="password" name="current_password" required>



<label>
New Password
</label>

<input type="password" name="new_password" required>



<label>
Confirm Password
</label>

<input type="password" name="confirm_password" required>



<div class="buttons">


<a href="index.php" class="back-btn">
<i class="fa-solid fa-arrow-left"></i>
Back
</a>



<button name="change">
<i class="fa-solid fa-key"></i>
Change Password
</button>


</div>


</form>


</div>


</div>


<?php

require_once "../../includes/footer.php";

?>