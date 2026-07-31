<?php

$page = "settings";

session_start();

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


$user_id = $_SESSION['user_id'] ?? 1;


// Get User

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id=?
");

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);



if(isset($_POST['update'])){


    $full_name = $_POST['full_name'];
    $username  = $_POST['username'];


    $update = $pdo->prepare("
        UPDATE users
        SET full_name=?,
            username=?
        WHERE id=?
    ");


    $update->execute([
        $full_name,
        $username,
        $user_id
    ]);


    echo "
    <script>
    alert('Profile Updated Successfully');
    window.location='profile.php';
    </script>
    ";

}



require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>


<link rel="stylesheet" href="../../assets/css/settings.css">


<div class="profile-container">


<div class="profile-card">


<h2>
<i class="fa-solid fa-user"></i>
My Profile
</h2>



<form method="POST">


<label>
Full Name
</label>

<input type="text" 
name="full_name"
value="<?= $user['full_name']; ?>"
required>



<label>
Username
</label>

<input type="text"
name="username"
value="<?= $user['username']; ?>"
required>



<label>
Role
</label>

<input type="text"
value="<?= $user['role']; ?>"
readonly>



<label>
Status
</label>

<input type="text"
value="<?= $user['status']; ?>"
readonly>




<div class="profile-buttons">


<a href="index.php" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>
Back

</a>


<button name="update">

<i class="fa-solid fa-save"></i>
Update Profile

</button>


</div>


</form>


</div>


</div>



<?php

require_once "../../includes/footer.php";

?>