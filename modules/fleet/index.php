<?php

$page = "fleet";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



$stmt = $pdo->query("
SELECT *
FROM vehicles
ORDER BY vehicle_id DESC
");


$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">

<head>
   
    <title></title>
    
    <link rel="stylesheet" href="../../assets/css/fleet.css">

</head>

    <body>

        <div class="fleet-container">


            <div class="page-header">


            <h2>
                🚚 Fleet Management
            </h2>

            <button class="add-btn" onclick="openVehicleModal()">
                + Add Vehicle
            </button>

        </div>

        <div class="search-box">

            <input 
            type="text"
            id="searchVehicle"
            placeholder="Search Vehicle No / Driver...">

        </div>

        <table class="fleet-table">


            <thead>

                <tr>

                    <th>Vehicle No</th>

                    <th>Type</th>

                    <th>Model</th>

                    <th>Driver</th>

                    <th>Status</th>

                    <th>Service Date</th>

                    <th>Fuel</th>

                    <th>Action</th>


                </tr>


            </thead>



            <tbody id="vehicleTable">


                <?php foreach($vehicles as $row){ ?>


                <tr>


                    <td>
                    <?= $row['vehicle_no']; ?>
                    </td>



                    <td>
                    <?= $row['vehicle_type']; ?>
                    </td>



                    <td>
                    <?= $row['model']; ?>
                    </td>



                    <td>

                    <?= $row['driver_name']; ?>

                    <br>

                    <small>
                    <?= $row['driver_phone']; ?>
                    </small>

                    </td>




                    <td>


                    <?php if($row['status']=="Available"){ ?>


                    <span class="status available">

                    Available

                    </span>


                    <?php }elseif($row['status']=="On Delivery"){ ?>


                    <span class="status delivery">

                    On Delivery

                    </span>


                    <?php }else{ ?>


                    <span class="status maintenance">

                    Maintenance

                    </span>


                    <?php } ?>


                    </td>





                    <td>

                    <?= $row['last_service_date']; ?>

                    <br>

                    <small>
                    <?= $row['last_service_details']; ?>
                    </small>


                    </td>




                    <td>

                    <?= $row['daily_fuel_liters']; ?> L

                    <br>

                    Rs. <?= number_format($row['daily_fuel_cost'],2); ?>


                    </td>





                    <td>


                    <a href="view_vehicle.php?id=<?= $row['vehicle_id']; ?>"
                    class="view-btn">

                    👁 View

                    </a>



                    <a href="delete_vehicle.php?id=<?= $row['vehicle_id']; ?>"
                    class="delete-btn"
                    onclick="return confirm('Delete this vehicle?')">

                    🗑 Delete

                    </a>



                    </td>


                </tr>



                <?php } ?>



            </tbody>


        </table>



        </div>

        <div id="vehicleModal" class="modal">


            <div class="modal-box">


                <span class="close"
                onclick="closeVehicleModal()">

                &times;

                </span>


                <h2>
                🚚 Add Vehicle
                </h2>



                <form action="save_vehicle.php" method="POST">


                    <label>Vehicle Number</label>

                    <input type="text" name="vehicle_no" required>



                    <label>Vehicle Type</label>

                    <select name="vehicle_type">

                    <option>Lorry</option>
                    <option>Truck</option>
                    <option>Van</option>
                    <option>Bike</option>

                    </select>



                    <label>Model</label>

                    <input type="text" name="model">



                    <label>Driver Name</label>

                    <input type="text" name="driver_name">



                    <label>Driver Phone</label>

                    <input type="text" name="driver_phone">



                    <label>Status</label>

                    <select name="status">

                    <option>Available</option>
                    <option>On Delivery</option>
                    <option>Maintenance</option>

                    </select>



                    <label>Last Service Date</label>

                    <input type="date" name="last_service_date">



                    <label>Service Details</label>

                    <textarea name="last_service_details"></textarea>



                    <label>Service Cost</label>

                    <input type="number" name="last_service_cost">



                    <label>Daily Fuel Liters</label>

                    <input type="number" step="0.01" name="daily_fuel_liters">



                    <label>Daily Fuel Cost</label>

                    <input type="number" step="0.01" name="daily_fuel_cost">



                    <label>Notes</label>

                    <textarea name="notes"></textarea>



                    <button type="submit" class="save-btn">

                    Save Vehicle

                    </button>


                </form>


            </div>


        </div>

        <script>


        document.getElementById("searchVehicle")
        .addEventListener("keyup",function(){


        let value=this.value.toLowerCase();


        let rows=document.querySelectorAll("#vehicleTable tr");



        rows.forEach(row=>{


        row.style.display =
        row.innerText.toLowerCase().includes(value)
        ? ""
        : "none";


        });


        });

        function openVehicleModal(){

            document.getElementById("vehicleModal").style.display="flex";

        }


        function closeVehicleModal(){

            document.getElementById("vehicleModal").style.display="none";

        }


        </script>
    </body>
</html>


<?php require_once '../../includes/footer.php'; ?>


