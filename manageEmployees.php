<?php
require_once('header.php');

if(isset($_POST['deleteEmployee'])){
	$emp_id = $_POST['deleteEmployee'];
	$sqlDelete = "DELETE FROM employees WHERE emp_id = '$emp_id'";
	if($conn->query($sqlDelete) === true){
		echo "
			<script type='text/javascript'>alert('Employee deleted successfully!')</script>
			";
	} else {
		echo "
			<script type='text/javascript'>alert('Error: ".$conn->error."')</script>
			";
	}
}

if(isset($_POST['submitEmployee'])){
	//The following takes the POST data and 'filters' it before inserting into the database to prevent SQL injection attacks. 
	//All user input should be 'filtered' before inserting into the database.
	
	$empFname = strip_tags($_POST['empFname']);
	$empLname = strip_tags($_POST['empLname']);
			
	$empFname = stripslashes($empFname);
	$empLname = stripslashes($empLname);

    $empFname = mysqli_real_escape_string($conn, $empFname);
    $empLname = mysqli_real_escape_string($conn, $empLname);
	
	$sqlInsert = $conn->prepare("INSERT INTO employees(emp_fname, emp_lname) 
					VALUES(?, ?)");
	$sqlInsert->bind_param('ss', $empFname, $empLname);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Success! ".$empFname." ".$empLname." has been added to the database!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

$sqlEmployees = "SELECT * FROM employees";
$employeesResult = $conn->query($sqlEmployees);
?>

<div class='container-fluid'>
	<h2>Employees</h2>
	<div class='row'>
		<div class='col-sm'>
			<div class='alert alert-primary'>
				Note: An employee must not have any assignments to be deleted.
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#addEmployee">Add Employee</button>
	<div id="addEmployee" class="collapse">
		<form action='manageEmployees.php' method='post' enctype='multipart/form-data'>
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<label for='empFname'>Employee First Name:</label>
						<input type='text' class='form-control' name='empFname' id='empFname' required>
					</div>
				</div>
			</div>
			
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<label for='empFname'>Employee Last Name:</label>
						<input type='text' class='form-control' name='empLname' id='empLname' required>
					</div>
				</div>
			</div>
			<input name='submitEmployee' type='submit' value='Submit'>
		</form>
	</div>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Name</th>
				<th>Manage</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			<?php
			while($row = $employeesResult->fetch_assoc()){
				$empName = $row['emp_fname']." ".$row['emp_lname'];
				echo "
					<tr>
						<td>".$empName."</td>
						<td>
							<form action='manageEmployee.php' method='post'>
								<button type='submit' class='btn btn-primary' name ='manageEmployee' value='".$row['emp_id']."'>Manage</button>
							</form>
						</td>
						<td>
							<form action='#' method='post'>
								<button type='submit' class='btn btn-danger' name ='deleteEmployee' value='".$row['emp_id']."'>Delete</button>
							</form>
						</td>
					</tr>
				";
			}
			?>
		</tbody>
	</table>
</div>
		
<?php
require_once('footer.php');
?>