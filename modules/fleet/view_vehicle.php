<?php

$page = "fleet";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



$id = $_GET['id'];



$stmt = $pdo->prepare("
SELECT *
FROM vehicles
WHERE vehicle_id = :id
");


$stmt->execute([

":id"=>$id

]);


$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);



?>

<link rel="stylesheet" href="../../assets/css/fleet.css">



<div class="fleet-container">


<div class="page-header">

<h2>
🚚 Vehicle Details
</h2>


<a href="index.php" class="delete-btn">
← Back
</a>

</div>




<div class="vehicle-card">


<h3>
Vehicle Information
</h3>


<p>
<b>Vehicle No :</b>
<?= $vehicle['vehicle_no']; ?>
</p>


<p>
<b>Type :</b>
<?= $vehicle['vehicle_type']; ?>
</p>


<p>
<b>Model :</b>
<?= $vehicle['model']; ?>
</p>


<p>
<b>Status :</b>
<?= $vehicle['status']; ?>
</p>


</div>





<div class="vehicle-card">


<h3>
Driver Details
</h3>


<p>
<b>Name :</b>
<?= $vehicle['driver_name']; ?>
</p>


<p>
<b>Phone :</b>
<?= $vehicle['driver_phone']; ?>
</p>


</div>






<div class="vehicle-card">


<h3>
Service Details
</h3>


<p>
<b>Last Service Date :</b>

<?= $vehicle['last_service_date']; ?>

</p>


<p>
<b>Details :</b>

<?= $vehicle['last_service_details']; ?>

</p>


<p>
<b>Cost :</b>

Rs. <?= number_format($vehicle['last_service_cost'],2); ?>

</p>


</div>






<div class="vehicle-card">


<h3>
Fuel Details
</h3>


<p>
<b>Daily Fuel :</b>

<?= $vehicle['daily_fuel_liters']; ?> L

</p>


<p>
<b>Fuel Cost :</b>

Rs. <?= number_format($vehicle['daily_fuel_cost'],2); ?>

</p>


</div>




<div class="vehicle-card">


<h3>
Notes
</h3>


<p>

<?= $vehicle['notes']; ?>

</p>


</div>




</div>



<?php require_once '../../includes/footer.php'; ?>