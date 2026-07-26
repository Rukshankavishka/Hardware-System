<?php

$page = "employees";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/navbar.php';


require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();



$stmt = $pdo->query("
SELECT *
FROM employees
ORDER BY employee_id DESC
");


$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<link rel="stylesheet" href="../../assets/css/employees.css">



<div class="employee-container">



<div class="page-header">


<h2>
👨‍💼 Employee Management
</h2>


<button class="add-btn"
onclick="openEmployeeModal()">

+ Add Employee

</button>


</div>




<div class="search-box">

<input type="text"
id="searchEmployee"
placeholder="Search employee...">

</div>






<table class="employee-table">


<thead>

<tr>

<th>Code</th>

<th>Name</th>

<th>Position</th>

<th>Phone</th>

<th>Salary</th>

<th>Status</th>

<th>Action</th>


</tr>

</thead>



<tbody id="employeeTable">


<?php foreach($employees as $row){ ?>


<tr>


<td>
<?= $row['employee_code']; ?>
</td>


<td>
<?= $row['name']; ?>
</td>


<td>
<?= $row['position']; ?>
</td>


<td>
<?= $row['phone']; ?>
</td>


<td>

Rs.
<?= number_format($row['basic_salary'],2); ?>

</td>



<td>


<?php if($row['status']=="Active"){ ?>

<span class="active-status">
Active
</span>

<?php }else{ ?>

<span class="inactive-status">
Inactive
</span>

<?php } ?>


</td>




<td>


<a href="view_employee.php?id=<?= $row['employee_id']; ?>"
class="view-btn">

👁 View

</a>



<a href="delete_employee.php?id=<?= $row['employee_id']; ?>"
class="delete-btn"
onclick="return confirm('Delete this employee?')">

🗑 Delete

</a>


</td>



</tr>


<?php } ?>


</tbody>


</table>


</div>

<!-- Add Employee Modal -->

<div id="employeeModal" class="modal">


<div class="modal-box">


<span class="close"
onclick="closeEmployeeModal()">
&times;
</span>



<h2>
👨‍💼 Add Employee
</h2>



<form action="save_employee.php" method="POST">



<label>Employee Name</label>

<input type="text" name="name" required>




<label>NIC Number</label>

<input type="text" name="nic">




<label>Phone Number</label>

<input type="text" name="phone">




<label>Address</label>

<textarea name="address"></textarea>




<label>Position</label>

<select name="position">


<option>Cashier</option>

<option>Store Keeper</option>

<option>Driver</option>

<option>Helper</option>

<option>Manager</option>


</select>





<label>Join Date</label>

<input type="date" name="join_date">





<label>Basic Salary</label>

<input type="number" 
step="0.01"
name="basic_salary">





<label>Status</label>

<select name="status">


<option>Active</option>

<option>Inactive</option>


</select>





<button type="submit" class="save-btn">

Save Employee

</button>



</form>



</div>


</div>




<script>


document.getElementById("searchEmployee")
.addEventListener("keyup",function(){


let value=this.value.toLowerCase();


document.querySelectorAll("#employeeTable tr")
.forEach(row=>{


row.style.display =
row.innerText.toLowerCase().includes(value)
? ""
: "none";


});


});



function openEmployeeModal(){

document.getElementById("employeeModal").style.display="flex";

}



function closeEmployeeModal(){

document.getElementById("employeeModal").style.display="none";

}

</script>




<?php require_once '../../includes/footer.php'; ?>