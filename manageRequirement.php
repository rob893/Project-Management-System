<?php
require_once('header.php');

if(!isset($_POST['removeAssignment']) && !isset($_POST['submitAssignment'])){
	$reqID = $_POST['manage'];
}

if(isset($_POST['removeAssignment'])){
	$reqID = $_POST['reqID'];
	$projID = $_POST['projID'];
	$reqName = $_POST['reqName'];
	$assignID = $_POST['removeAssignment'];
	$empName = $_POST['empName'];
	
	$sqlDelete = "DELETE FROM assignment WHERE assign_id = '$assignID'";
	$sqlRemoveHours = "DELETE FROM hours_worked WHERE assign_id = '$assignID'";
	if(($conn->query($sqlDelete) === true) && ($conn->query($sqlRemoveHours) === true)){
		echo "<script type='text/javascript'>alert('Success! ".$empName." has been removed from ".$reqName."!')</script>";
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$conn->error."')</script>";
	}
}

if(isset($_POST['submitAssignment'])){
	list($empName, $empId) = explode("-" , $_POST['empName'], 2);
	$reqID = $_POST['reqID'];
	$projID = $_POST['projID'];
	$reqName = $_POST['reqName'];
	
	$sqlAssignmentTest = "
		SELECT * 
		FROM assignment 
		WHERE emp_id = '$empId' AND req_id = '$reqID'";
	$assignmentTestResult = $conn->query($sqlAssignmentTest);
	
	if($assignmentTestResult->fetch_assoc() === null){ //if the employee has not already been assigned to this requirement.
		$sqlInsert = $conn->prepare("INSERT INTO assignment(req_id, proj_id, emp_id) VALUES(?, ?, ?)");
		$sqlInsert->bind_param('iii', $reqID, $projID, $empId);
		
		if($sqlInsert->execute() === true){
			echo "<script type='text/javascript'>alert('Success! ".$empName." has been assinged to ".$reqName."!')</script>";
			$sqlInsert->close();
		} else {
			echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
			$sqlInsert->close();
		}
	} else { //If the employee is already assigned to this requirement.
		echo "<script type='text/javascript'>alert('Error: ".$empName." has already been assigned to ".$reqName."! An employee cannot be assigned to the same requirement twice.')</script>";
	}
}

$sqlRequirements = "
	SELECT * 
	FROM requirements
	INNER JOIN assignment ON requirements.req_id = assignment.req_id
	INNER JOIN project ON requirements.proj_id = project.proj_id
	INNER JOIN employees ON assignment.emp_id = employees.emp_id
	WHERE requirements.req_id = '$reqID'";
$sqlEmployees = "SELECT * FROM employees";
$sqlRequirements2 = "
	SELECT * FROM requirements 
	INNER JOIN project ON requirements.proj_id = project.proj_id
	WHERE req_id = '$reqID'";
					
$basicInfoResults = $conn->query($sqlRequirements);
$nameResults = $conn->query($sqlEmployees);

$infoRows = $basicInfoResults->fetch_assoc();

if($infoRows['req_id'] === null){ //If a requirement does not have an assignment, $sqlRequirements will return nothing. This section ensures that a requirement with no assignment will still work.
	$basicInfoResults2 = $conn->query($sqlRequirements2);
	$infoRows2 = $basicInfoResults2->fetch_assoc();
	
	$infoRows['proj_id'] = $infoRows2['proj_id'];
	$infoRows['req_name'] = $infoRows2['req_name'];
	$infoRows['proj_name']  = $infoRows2['proj_name'];
	$infoRows['req_desc'] = $infoRows2['req_desc'];
}
?>
<div class='container-fluid'>
	<h3>Project Name:</h3>
	<p><?php echo $infoRows['proj_name']; ?></p>
	<h3>Requirement Name:</h3>
	<p><?php echo $infoRows['req_name']; ?></p>
	<br>
	<h3>Requirement Description:</h3>
	<p><?php echo $infoRows['req_desc']; ?></p>
	<br>
	<h3>Assigned Employees and Hours Worked:</h3>
	<div class='row'>
		<div class='col-sm'>
			<div class='alert alert-primary'>
				Note: The hours per employee are all logged hours of that employee. To view each time the employee has logged hours, 
				click "Manage Employees" in the nav bar and then the "Manage" button next to the employee.
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='col-sm'>
			<div class='alert alert-danger'>
				<strong>WARNING!</strong> Deleting an assignment will also delete that employee's hours worked on that assignment.
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#assignEmployee">Assign Employee to this Requirement</button>
	<div id="assignEmployee" class="collapse">
		<form action='manageRequirement.php' method='post' enctype='multipart/form-data'>				
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<label for='empName'>Select Employee to Assign:</label>
						<select class='form-control' id='empName' name='empName'>
						<?php
						while($row = $nameResults->fetch_assoc()){
							$empName = $row['emp_fname']." ".$row['emp_lname'];
							$empId = $row['emp_id'];
							echo "
								<option value='".$empName."-".$empId."'>".$empName."</option>
							";
						}
						?>
						</select>
					</div>
				</div>
			</div>
			<input type='hidden' name='reqID' value='<?php echo $reqID; ?>'>
			<input type='hidden' name='projID' value='<?php echo $infoRows['proj_id']; ?>'>
			<input type='hidden' name='reqName' value='<?php echo $infoRows['req_name']; ?>'>
			<input name='submitAssignment' type='submit' value='Submit'>
		</form>
	</div>

	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Name</th>
				<th>Requirements Analysis</th>
				<th>Design</th>
				<th>Coding</th>
				<th>Testing</th>
				<th>Management</th>
				<th>Total Hours</th>
				<th>Delete Assignment</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$totalReqHours = 0;
			$totalReqAna = 0;
			$totalDesign = 0;
			$totalCoding = 0;
			$totalTesting = 0;
			$totalManagement = 0;
			$assignTest = $conn->query($sqlRequirements);
			if($assignTest->fetch_assoc() === null){
				echo "
					<tr>
						<td>No Assignments.</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				";
			} else {
				$requirementsResults = $conn->query($sqlRequirements);
				while($rowReq = $requirementsResults->fetch_assoc()){
					$empTotalHours = 0;
					$sqlHours = "
						SELECT SUM(req_ana_hours) AS total_req_ana, SUM(design_hours) AS total_design, SUM(coding_hours) AS total_coding, SUM(testing_hours) AS total_testing, 
							SUM(management_hours) AS total_management, emp_fname, emp_lname, assignment.assign_id AS assignment_id
						FROM hours_worked
						INNER JOIN assignment ON assignment.assign_id = hours_worked.assign_id
						INNER JOIN employees ON assignment.emp_id = employees.emp_id
						WHERE assignment.emp_id = '$rowReq[emp_id]' AND assignment.req_id = '$rowReq[req_id]'";
					$hoursResults = $conn->query($sqlHours);
					while($row = $hoursResults->fetch_assoc()){
						$empTotalHours = $row['total_req_ana'] + $row['total_design'] + $row['total_coding'] + $row['total_testing'] + $row['total_management'];
						if($row['total_req_ana'] === null){
							$row['total_req_ana'] = 0;
						}
						if($row['total_design'] === null){
							$row['total_design'] = 0;
						}
						if($row['total_coding'] === null){
							$row['total_coding'] = 0;
						}
						if($row['total_testing'] === null){
							$row['total_testing'] = 0;
						}
						if($row['total_management'] === null){
							$row['total_management'] = 0;
						}
						if($row['assignment_id'] === null){ //If the employee has been assigned to a requirement but has not yet logged any hours
							$sqlAssignId = "
								SELECT assign_id 
								FROM assignment 
								WHERE req_id = '$reqID' AND emp_id = '$rowReq[emp_id]'";
							$assignIdResult = $conn->query($sqlAssignId);
							$assignRow = $assignIdResult->fetch_assoc();
							$row['assignment_id'] = $assignRow['assign_id'];
						}
						$totalReqHours += $empTotalHours;
						$totalReqAna += $row['total_req_ana'];
						$totalDesign += $row['total_design'];
						$totalCoding += $row['total_coding'];
						$totalTesting += $row['total_testing'];
						$totalManagement += $row['total_management'];
						echo "
							<tr>
								<td>".$row['emp_fname']." ".$row['emp_lname']."</td>
								<td>".$row['total_req_ana']."</td>
								<td>".$row['total_design']."</td>
								<td>".$row['total_coding']."</td>
								<td>".$row['total_testing']."</td>
								<td>".$row['total_management']."</td>
								<td>".$empTotalHours."</td>
								<td>
									<form action='manageRequirement.php' method='post'>
										<input type='hidden' name='reqID' value='".$reqID."'>
										<input type='hidden' name='projID' value='".$infoRows['proj_id']."'>
										<input type='hidden' name='reqName' value='".$infoRows['req_name']."'>
										<input type='hidden' name='empName' value='".$row['emp_fname']." ".$row['emp_lname']."'>
										<button type='submit' class='btn btn-danger' name ='removeAssignment' value='".$row['assignment_id']."'>Delete</button>
									</form>
								</td>
							</tr>
						";
					}
				}
			}
			?>
			<tr>
				<td><b>Totals</b></td>
				<td><b><?php echo $totalReqAna; ?></b></td>
				<td><b><?php echo $totalDesign; ?></b></td>
				<td><b><?php echo $totalCoding; ?></b></td>
				<td><b><?php echo $totalTesting; ?></b></td>
				<td><b><?php echo $totalManagement; ?></b></td>
				<td><b><?php echo $totalReqHours; ?></b></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	
	<form action='manageProject.php' method='post'>
		<button type='submit' class='btn btn-primary' name ='manage' value='<?php echo $infoRows['proj_id']; ?>'>Back to Project</button>
	</form>
</div>

<?php
require_once('footer.php');
?>