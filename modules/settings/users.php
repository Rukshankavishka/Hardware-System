<?php

$page = "settings";

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();


// Get Users

$stmt = $pdo->query("
    SELECT *
    FROM users
    ORDER BY id DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>


<link rel="stylesheet" href="../../assets/css/settings.css">


<div class="users-container">


    <div class="users-header">

        <div>
            <h2>
                <i class="fa-solid fa-users"></i>
                User Management
            </h2>

            <p>
                Manage system users and access roles
            </p>
        </div>


        <a href="add_user.php" class="add-user-btn">
            <i class="fa-solid fa-plus"></i>
            Add New User
        </a>

        <a href="../../modules/settings/index.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>

    </div>



    <div class="users-card">


        <table>


            <thead>

                <tr>

                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Action</th>

                </tr>

            </thead>



            <tbody>


            <?php foreach($users as $user): ?>


                <tr>

                    <td>
                        <?= $user['id']; ?>
                    </td>


                    <td>
                        <?= $user['full_name']; ?>
                    </td>


                    <td>
                        <?= $user['username']; ?>
                    </td>


                    <td>

                        <span class="role">

                        <?= $user['role']; ?>

                        </span>

                    </td>


                    <td>

                        <?php if($user['status']=="active"): ?>

                            <span class="active">
                                Active
                            </span>

                        <?php else: ?>

                            <span class="inactive">
                                Inactive
                            </span>

                        <?php endif; ?>

                    </td>


                    <td>
                        <?= $user['created_at']; ?>
                    </td>


                    <td>

                        <a href="edit_user.php?id=<?= $user['id']; ?>" 
                        class="edit-btn">

                        <i class="fa-solid fa-pen"></i>

                        </a>


                        <a href="delete_user.php?id=<?= $user['id']; ?>"
                        class="delete-btn"
                        onclick="return confirm('Are you sure delete this user?');">

                        <i class="fa-solid fa-trash"></i>

                        </a>


                    </td>


                </tr>


            <?php endforeach; ?>


            </tbody>


        </table>


    </div>



</div>



<?php

require_once "../../includes/footer.php";

?>