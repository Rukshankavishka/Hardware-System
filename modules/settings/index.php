<?php

require_once '../../includes/auth.php';

$page = "settings";

require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>

<link rel="stylesheet" href="../../assets/css/settings.css">


<div class="settings-container">

    <div class="settings-header">
        <h2>
            <i class="fa-solid fa-gear"></i>
            Settings
        </h2>
    </div>


    <div class="settings-cards">


        <!-- Shop Information -->
        <div class="setting-card">

            <i class="fa-solid fa-store"></i>

            <h3>Shop Information</h3>

            <p>
                Update shop name, address and contact details.
            </p>

            <a href="shop_info.php" class="setting-btn">
                Manage
            </a>

        </div>



        <!-- Profile -->
        <div class="setting-card">

            <i class="fa-solid fa-user"></i>

            <h3>My Profile</h3>

            <p>
                Update your personal information.
            </p>

            <a href="profile.php" class="setting-btn">
                Manage
            </a>

        </div>




        <!-- Password -->
        <div class="setting-card">

            <i class="fa-solid fa-lock"></i>

            <h3>Change Password</h3>

            <p>
                Change your account password.
            </p>

            <a href="change_password.php" class="setting-btn">
                Manage
            </a>

        </div>




        <!-- Users -->
        <div class="setting-card">

            <i class="fa-solid fa-users"></i>

            <h3>User Management</h3>

            <p>
                Add and manage system users.
            </p>

            <a href="users.php" class="setting-btn">
                Manage
            </a>

        </div>


    </div>


</div>


<?php

require_once "../../includes/footer.php";

?>