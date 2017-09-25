<?php
require_once('header.php');

if(!isset($_POST['empName']) && !isset($_POST['assignment']) && !isset($_POST['submitHours'])){
	$sqlEmp = "SELECT * FROM employees";
	$empResults = $conn->query($sqlEmp);
	?>
	<div class='container-fluid'>
		<h3>Select employee to log hours for</h3>
		<form action='logHours.php' method='post' enctype='multipart/form-data'>				
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<select class='form-control' id='empName' name='empName'>
						<?php
						while($row = $empResults->fetch_assoc()){
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
			<input name='submitEmp' type='submit' value='Submit'>
		</form>
	</div>
	<?php
}

if(isset($_POST['empName'])){
	list($empName, $empId) = explode("-", $_POST['empName'], 2);
	
	$sqlAssignments = "SELECT * FROM assignment
						INNER JOIN requirements ON assignment.req_id = requirements.req_id
						INNER JOIN employees ON assignment.emp_id = employees.emp_id
						INNER JOIN project ON assignment.proj_id = project.proj_id
						WHERE assignment.emp_id =  '$empId'";
	$assignmentsResults = $conn->query($sqlAssignments);
	?>
	<div class='container-fluid'>
		<h3>Employee: <?php echo $empName; ?></h3>
		<br>
		<h3>Select an assignment to log hours for</h3>
		<form action='logHours.php' method='post' enctype='multipart/form-data'>				
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						<select class='form-control' id='assignment' name='assignment'>
						<?php
						while($row = $assignmentsResults->fetch_assoc()){
							echo "
								<option value='".$row['assign_id']."-".$row['emp_id']."-".$row['proj_id']."-".$row['req_id']."'>Project: ".$row['proj_name']." Requirement: ".$row['req_name']."</option>
							";
						}
						?>
						</select>
					</div>
				</div>
			</div>
			<input name='submitAssignment' type='submit' value='Submit'>
		</form>
	</div>
	<?php
}

if(isset($_POST['assignment'])){
	list($assignId, $empId, $projId, $reqId) = explode("-", $_POST['assignment'], 4);
	$sqlAssignment = "SELECT * FROM assignment
						INNER JOIN requirements ON assignment.req_id = requirements.req_id
						INNER JOIN employees ON assignment.emp_id = employees.emp_id
						INNER JOIN project ON assignment.proj_id = project.proj_id
						WHERE assign_id = '$assignId'";
	$assignmentResults = $conn->query($sqlAssignment);
	$assignmentRow = $assignmentResults->fetch_assoc();
	?>
	<div class='container-fluid'>
		<h3>Employee: <?php echo $assignmentRow['emp_fname']." ".$assignmentRow['emp_lname']; ?></h3>
		<h3>Assignment: <?php echo $assignmentRow['req_name']." for ".$assignmentRow['proj_name']; ?></h3>
		<br>
		<h3>Input Hours:</h3>
		<form action='logHours.php' method='post' enctype='multipart/form-data'>
			<div class='row'>
				<div class='col-sm-2'>
					<label for="date" class="col-form-label">Date</label>
					<input type='date' class='form-control' name='date' id='date' required>
				</div>
			</div>
			<div class='row'>
				<div class='col'>
					<label for="reqAna" class="col-form-label">Requirement Analysis Hours</label>
					<input type='number' class='form-control' name='reqAnaHours' id='reqAna' required>
				</div>
				<div class='col'>
					<label for="design" class="col-form-label">Design Hours</label>
					<input type='number' class='form-control' name='designHours' id='design' required>
				</div>
				<div class='col'>
					<label for="coding" class="col-form-label">Coding Hours</label>
					<input type='number' class='form-control' name='codingHours' id="coding" required>
				</div>
				<div class='col'>
					<label for="testing" class="col-form-label">Testing Hours</label>
					<input type='number' class='form-control' name='testingHours' id='testing' required>
				</div>
				<div class='col'>
					<label for="management" class="col-form-label">Management Hours</label>
					<input type='number' class='form-control' name='managementHours' id='management' required>
				</div>
			</div>
			<br>
			<input type='hidden' name='reqId' value='<?php echo $reqId; ?>'>
			<input type='hidden' name='projId' value='<?php echo $projId; ?>'>
			<input type='hidden' name='assignId' value='<?php echo $assignId; ?>'>
			<input type='hidden' name='empId' value='<?php echo $empId; ?>'>
			<input name='submitHours' type='submit' value='Submit'>
		</form>
	</div>
	<?php
}

if(isset($_POST['submitHours'])){
	$assignId = $_POST["assignId"];
	$empId = $_POST["empId"];
	$reqId = $_POST["reqId"];
	$projId = $_POST["projId"];
	$date = $_POST["date"];
	$reqAnaHours = $_POST["reqAnaHours"];
	$designHours = $_POST["designHours"];
	$codingHours = $_POST["codingHours"];
	$testingHours = $_POST["testingHours"];
	$managementHours = $_POST["managementHours"];
	
	$sqlInsertHours = $conn->prepare("INSERT INTO hours_worked(assign_id, emp_id, req_id, proj_id, date, req_ana_hours, design_hours, coding_hours, testing_hours, management_hours)
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$sqlInsertHours->bind_param('iiiisiiiii', $assignId, $empId, $reqId, $projId, $date, $reqAnaHours, $designHours, $codingHours, $testingHours, $managementHours);
	if($sqlInsertHours->execute() === true){
		echo "<script type='text/javascript'>alert('Success! Hours have been logged!')</script>
			<div class='container-fluid'>
				<h3>Hours have been logged!</h3>
			</div>";
		$sqlInsertHours->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsertHours->error."')</script>
			<div class='container-fluid'>
				<h3>An error has occurred. Hours have not been logged.</h3>
			</div>";
		$sqlInsertHours->close();
	}
}
require_once('footer.php');
?>