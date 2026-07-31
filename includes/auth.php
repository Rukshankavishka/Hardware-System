<?php

session_start();


// Check Login

if(!isset($_SESSION['user_id'])){

    header("Location: /hardware_system/login.php");

    exit();

}

?>