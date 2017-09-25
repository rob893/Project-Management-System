<?php
require_once('header.php');

if(!isset($_POST['delete']) && !isset($_POST['submit']) && !isset($_POST['deleteRisk']) && !isset($_POST['submitRisk'])){ //default if no post vars from this page is set (mostly to keep error log from complaining).
	$projID = $_POST['manage']; //this comes from the passed in project id from manageProjects.php
}

if(isset($_POST['delete'])){
	list($req_id, $projID) = explode("-" , $_POST['delete'], 2);
	$sqlDelete = "DELETE FROM requirements WHERE req_id = '$req_id'";
	if($conn->query($sqlDelete) === true){
		echo "
			<script type='text/javascript'>alert('Requirement deleted successfully!')</script>
			";
	} else {
		echo "
			<script type='text/javascript'>alert('Error: ".$conn->error."')</script>
			";
	}
}

if(isset($_POST['deleteRisk'])){
	list($risk_id, $projID) = explode("-" , $_POST['deleteRisk'], 2);
	$sqlDelete = "DELETE FROM risks WHERE risk_id = '$risk_id'";
	if($conn->query($sqlDelete) === true){
		echo "
			<script type='text/javascript'>alert('Risk deleted successfully!')</script>
			";
	} else {
		echo "
			<script type='text/javascript'>alert('Error: ".$conn->error."')</script>
			";
	}
}

if(isset($_POST['submit'])){
	$projID = $_POST['projID'];
	//The following takes the POST data and 'filters' it before inserting into the database to prevent SQL injection attacks. 
	//All user input should be 'filtered' before inserting into the database.
	
	$reqName = strip_tags($_POST['reqName']);
	$reqDescription = strip_tags($_POST['reqDescription']);
			
	$reqName = stripslashes($reqName);
	$reqDescription = stripslashes($reqDescription);

    $reqName = mysqli_real_escape_string($conn, $reqName);
    $reqDescription = mysqli_real_escape_string($conn, $reqDescription);
	
	$sqlInsert = $conn->prepare("INSERT INTO requirements(req_name, req_desc, proj_id) 
					VALUES(?, ?, ?)");
	$sqlInsert->bind_param('ssi', $reqName, $reqDescription, $projID);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Success! The requirement named ".$reqName." has been added to the database!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_POST['submitRisk'])){
	$projID = $_POST['projID'];
	//The following takes the POST data and 'filters' it before inserting into the database to prevent SQL injection attacks. 
	//All user input should be 'filtered' before inserting into the database.
	
	$riskName = strip_tags($_POST['riskName']);
	$riskDescription = strip_tags($_POST['riskDescription']);
	$riskStatus = strip_tags($_POST['riskStatus']);
			
	$riskName = stripslashes($riskName);
	$riskDescription = stripslashes($riskDescription);
	$riskStatus = stripslashes($riskStatus);

    $riskName = mysqli_real_escape_string($conn, $riskName);
    $riskDescription = mysqli_real_escape_string($conn, $riskDescription);
	$riskStatus = mysqli_real_escape_string($conn, $riskStatus);
	
	$sqlInsert = $conn->prepare("INSERT INTO risks(risk_name, risk_desc, risk_status, proj_id) 
					VALUES(?, ?, ?, ?)");
	$sqlInsert->bind_param('sssi', $riskName, $riskDescription, $riskStatus, $projID);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Success! The risk named ".$riskName." has been added to the database!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}
//End isset POST

$sqlProject = "SELECT * FROM project
				INNER JOIN employees ON project.owner_emp_id = employees.emp_id
				WHERE project.proj_id = '$projID'";
$sqlRequirements = "SELECT * FROM requirements WHERE proj_id = '$projID'";
$sqlAssignment = "SELECT DISTINCT assignment.emp_id, employees.emp_id, emp_fname, emp_lname, proj_id
					FROM assignment
					INNER JOIN employees ON assignment.emp_id = employees.emp_id
					WHERE proj_id = '$projID'";
$sqlRisks = "SELECT * FROM risks WHERE proj_id = '$projID'";

$projectResults = $conn->query($sqlProject);
$requirementsResults = $conn->query($sqlRequirements);
$assignmentResults = $conn->query($sqlAssignment);
$riskResults = $conn->query($sqlRisks);

$projectRows = $projectResults->fetch_assoc();

?>

<div class='container-fluid'>
	<h3>Project Name:</h3>
	<p><b><?php echo $projectRows['proj_name']; ?> </b></p>
	<br>
	<h3>Project Owner:</h3>
	<p><?php echo $projectRows['emp_fname']." ".$projectRows['emp_lname']; ?></p>
	<br>
	
	<h3>Project Team Members:</h3>
	<div class='row'>
		<div class='col-sm'>
			<div class='alert alert-primary'>
				Note: In order for an employee to be assigned to a project, that employee must be assigned to one of the project's requirements.
			</div>
		</div>
	</div>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Name</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$empTest = $conn->query($sqlAssignment);
			if($empTest->fetch_assoc() === null){
				echo "
					<tr>
						<td>No Assigned Employees</td>
					</tr>";
			} else {
				while($row = $assignmentResults->fetch_assoc()){
					echo "
						<tr>
							<td>".$row['emp_fname']." ".$row['emp_lname']."</td>
						</tr>";
				}
			}
			?>
		</tbody>
	</table>
	<br>
		
	<h3>Project Description:</h3>
	<p><?php echo $projectRows['proj_desc']; ?></p>
	<br>
	
	<h3>Project Requirements</h3>
	<div class='row'>
		<div class='col-sm'>
			<div class='alert alert-primary'>
				Note: A requirement must not have any assignments to be deleted.
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#addReq">Add a Requirement</button>
		<div id="addReq" class="collapse">
			<form action='manageProject.php' method='post' enctype='multipart/form-data'>
				<div class='row'>
					<div class='col-sm-2'>
						<div class='form-group'>
							<label for='reqName'>Requirement Name:</label>
							<input type='text' class='form-control' name='reqName' id='reqName' required>
						</div>
					</div>
				</div>
		
				<div class='row'>
					<div class='col-sm-4'>
						<div class='form-group'>
							<label for='reqDescription'>Requirement Description:</label>
							<textarea class='form-control' rows='5' name='reqDescription' id='reqDescription' required></textarea>
						</div>
					</div>
				</div>
				<input name='projID' type='hidden' value='<?php echo $projID; ?>'>
				<input name='submit' type='submit' value='Submit'>
			</form>
		</div>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Requirement Name</th>
				<th>Requirement Description</th>
				<th>Hours Worked on Requirement</th>
				<th>Manage</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$totalProjectHours = 0;
			$reqTest = $conn->query($sqlRequirements);
			if($reqTest->fetch_assoc() === null){
				echo "
					<tr>
						<td>No Requirements</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>";
			} else {
				while($row = $requirementsResults->fetch_assoc()){
					$sqlHours = "SELECT SUM(req_ana_hours + design_hours + coding_hours + testing_hours + management_hours) AS total_hours
									FROM hours_worked WHERE req_id = '$row[req_id]'";
					$hoursResults = $conn->query($sqlHours);
					$rowHours = $hoursResults->fetch_assoc();
					$hours = $rowHours['total_hours'];
					if($rowHours['total_hours'] === null){
						$hours = 0;
					}
					$totalProjectHours += $hours;
					
					echo "
						<tr>
							<td>".$row['req_name']."</td>
							<td>".$row['req_desc']."</td>
							<td>".$hours."</td>
							<td>
								<form action='manageRequirement.php' method='post'>
									<button type='submit' class='btn btn-primary' name ='manage' value='".$row['req_id']."'>Manage</button>
								</form>
							</td>
							<td>
								<form action='manageProject.php' method='post'>
									<button type='submit' class='btn btn-danger' name ='delete' value='".$row['req_id']."-".$projID."'>Delete</button>
								</form>
							</td>
						</tr>";
				}
			}
			?>
			<tr>
				<td><b>Total Project Hours</b></td>
				<td></td>
				<td><b><?php echo $totalProjectHours; ?></b></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<br>
	
	<h3>Project Risks</h3>
	<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#addRisk">Add a Risk</button>
	<div id="addRisk" class="collapse">
		<form action='manageProject.php' method='post' enctype='multipart/form-data'>
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<label for='riskName'>Risk Name:</label>
						<input type='text' class='form-control' name='riskName' id='riskName' required>
					</div>
				</div>
			</div>
			
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<label for='riskStatus'>Risk Status:</label>
						<input type='text' class='form-control' name='riskStatus' id='riskStatus' required>
					</div>
				</div>
			</div>
	
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						<label for='riskDescription'>Risk Description:</label>
						<textarea class='form-control' rows='5' name='riskDescription' id='riskDescription' required></textarea>
					</div>
				</div>
			</div>
			
			<input name='projID' type='hidden' value='<?php echo $projID; ?>'>
			<input name='submitRisk' type='submit' value='Submit'>
		</form>
	</div>
	
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Risk Name</th>
				<th>Risk Description</th>
				<th>Risk Status</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			<?php	
			$riskTest = $conn->query($sqlRisks);
			if($riskTest->fetch_assoc() === null){
				echo "
					<tr>
						<td>No Risks</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>";
			} else {
				while($row = $riskResults->fetch_assoc()){
					echo "
						<tr>
							<td>".$row['risk_name']."</td>
							<td>".$row['risk_desc']."</td>
							<td>".$row['risk_status']."</td>
							<td>
								<form action='manageProject.php' method='post'>
									<button type='submit' class='btn btn-danger' name ='deleteRisk' value='".$row['risk_id']."-".$projID."'>Delete</button>
								</form>
							</td>
						</tr>";
				}
			}
			?>
		</tbody>
	</table>
	<a href="manageProjects.php" class='btn btn-primary' role='button'>Back to Projects</a>
</div>

<?php
require_once('footer.php');
?>