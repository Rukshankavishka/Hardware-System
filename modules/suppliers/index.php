<?php

$page = "suppliers";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



$stmt = $pdo->query(
    "SELECT * FROM suppliers ORDER BY supplier_id DESC"
);


$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">

<head>
   
    <title></title>
    <link rel="stylesheet" href="../../assets/css/suppliers.css">

</head>

<body>




<div class="page-container">

<div class="top-bar">

<input type="text"
id="searchSupplier"
placeholder="Search Supplier...">

<button class="add-btn"
onclick="openSupplierModal()">

+ Add Supplier

</button>

</div>





<table class="product-table">


    <thead>

        <tr>

        <th>Supplier Code</th>

        <th>Supplier Name</th>

        <th>Company</th>

        <th>Phone</th>

        <th>Address</th>

        <th>Action</th>

        </tr>


    </thead>




    <tbody>


            <?php foreach($suppliers as $row){ ?>


            <tr>


            <td>
            <?= $row['supplier_code']; ?>
            </td>


            <td>
            <?= $row['supplier_name']; ?>
            </td>


            <td>
            <?= $row['company_name']; ?>
            </td>


            <td>
            <?= $row['phone']; ?>
            </td>


            <td>
            <?= $row['address']; ?>
            </td>



            <td>


            <button class="view-btn">

            👁 View

            </button>



            <button class="delete-btn"
            onclick="deleteSupplier(<?= $row['supplier_id']; ?>)">

            🗑 Delete

            </button>


            </td>


            </tr>


            <?php } ?>


    </tbody>


</table>



</div>





<!-- ADD SUPPLIER MODAL -->


<div id="supplierModal"
class="modal">


<div class="modal-box">


<span class="close"
onclick="closeSupplierModal()">

&times;

</span>



<h2>Add Supplier</h2>



<form action="add_suppliers.php"
method="POST">


<div class="form-group">

<label>Supplier Name</label>

<input type="text"
name="supplier_name"
required>

</div>




<div class="form-group">

<label>Company Name</label>

<input type="text"
name="company_name">

</div>





<div class="form-group">

<label>Phone</label>

<input type="text"
name="phone"
required>

</div>





<div class="form-group">

<label>Address</label>

<textarea name="address"></textarea>

</div>





<div class="form-group">

<label>Business Reg No</label>

<input type="text"
name="br_no">

</div>



<button class="save-btn">

Save Supplier

</button>



</form>



</div>


</div>





<script>


function openSupplierModal(){

document.getElementById("supplierModal")
.style.display="flex";

}



function closeSupplierModal(){

document.getElementById("supplierModal")
.style.display="none";

}




function deleteSupplier(id){

if(confirm("Delete this Supplier?")){


window.location =
"delete_supplier.php?id="+id;


}


}




document
.getElementById("searchSupplier")
.addEventListener("keyup",function(){


let value=this.value.toLowerCase();


let rows=document.querySelectorAll(
".product-table tbody tr"
);



rows.forEach(row=>{


let text=row.innerText.toLowerCase();


row.style.display =
text.includes(value) ? "" : "none";


});


});


</script>
</body>

<?php require_once '../../includes/footer.php'; ?>