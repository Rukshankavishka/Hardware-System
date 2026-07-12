<?php
session_start();

require_once 'config/database.php';

$database = new Database();
$conn = $database->connect();

$error = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {

        $sql = "SELECT * FROM users WHERE username = ? AND status = 'active'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // දැනට Plain Password (පස්සේ password_hash() වලට මාරු කරනවා)
            if ($password == $user['password']) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: modules/dashboard/index.php");
                exit;

            } else {
                $error = "Incorrect Password!";
            }

        } else {
            $error = "Username Not Found!";
        }

    } else {
        $error = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Hardware Shop Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/login.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <script src="assets/js/login.js" defer></script>

</head>

<body>

<div class="login-container">

    <!-- Left Side -->
    <div class="left-panel">

        <div class="overlay">

            <img src="assets/images/logo.png" class="logo" alt="Logo">

            <h1>THISARU</h1>

            <h2>Hardware Management System</h2>

            <p>
                Inventory • Billing • Customers • Suppliers • Employees
            </p>

        </div>

    </div>

    <!-- Right Side -->
    <div class="right-panel">

        <div class="login-card">

            <h2>Welcome Back</h2>

            <p>Sign in to continue</p>

            <?php if($error!=""){ ?>

            <div class="error-box">

                <?php echo $error; ?>

            </div>

            <?php } ?>

            <form method="POST">

                <div class="input-group">

                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        required>

                </div>

                <div class="input-group">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Password"
                        required>

                    <span class="toggle-password">

                        <i class="fa-solid fa-eye" id="togglePassword"></i>

                    </span>

                </div>

                <div class="options">

                    <label>

                        <input type="checkbox">

                        Remember Me

                    </label>

                </div>

                <button
                    type="submit"
                    name="login"
                    class="login-btn">

                    Login

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>