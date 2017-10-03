<?php
require_once('header.php');

$empId = $_POST['manageEmployee'];

$sqlEmployeeAssignments = "
	SELECT project.proj_name, requirements.req_id, requirements.req_name FROM employees
	INNER JOIN assignment ON employees.emp_id = assignment.emp_id
	INNER JOIN requirements ON assignment.req_id = requirements.req_id
	INNER JOIN project ON assignment.proj_id = project.proj_id
	WHERE employees.emp_id = '$empId'";
$sqlEmployee = "
	SELECT * FROM employees
	INNER JOIN assignment ON employees.emp_id = assignment.emp_id
	INNER JOIN requirements ON requirements.req_id = assignment.req_id
	INNER JOIN project ON project.proj_id = assignment.proj_id
	INNER JOIN hours_worked ON assignment.assign_id = hours_worked.assign_id
	WHERE employees.emp_id = '$empId'";
$sqlBasicInfo = "
	SELECT * 
	FROM employees 
	WHERE emp_id = '$empId'";

$employeeInfoResults = $conn->query($sqlBasicInfo);
$employeeBasicInfo = $employeeInfoResults->fetch_assoc();
?>
<div class='container-fluid'>
	<h3>Employee: <?php echo $employeeBasicInfo['emp_fname']." ".$employeeBasicInfo['emp_lname']; ?></h3>
	<br>
	<h3><?php echo $employeeBasicInfo['emp_fname']." ".$employeeBasicInfo['emp_lname']."'s assignments:"; ?></h3>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Project</th>
				<th>Requirement</th>
				<th>View Requirement</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$assignmentTest = $conn->query($sqlEmployeeAssignments);
			if($assignmentTest->fetch_assoc() === null){
				echo "
					<tr>
						<td>No Assignments.</td>
						<td></td>
						<td></td>
					</tr>
				";
			} else {
				$assignmentResults = $conn->query($sqlEmployeeAssignments);
				while($row = $assignmentResults->fetch_assoc()){
					echo "
						<tr>
							<td>".$row['proj_name']."</td>
							<td>".$row['req_name']."</td>
							<td>
								<form action='manageRequirement.php' method='post'>
									<button type='submit' class='btn btn-primary' name ='manage' value='".$row['req_id']."'>View Requirement</button>
								</form>
							</td>
						</tr>
					";
				}
			}
			?>
		</tbody>
	</table>
	<br>
	<h3><?php echo $employeeBasicInfo['emp_fname']." ".$employeeBasicInfo['emp_lname']."'s logged hours:"; ?></h3>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Date Logged</th>
				<th>Project</th>
				<th>Requirement</th>
				<th>Requirement Analysis Hours</th>
				<th>Design Hours</th>
				<th>Coding Hours</th>
				<th>Testing Hours</th>
				<th>Management Hours</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$employeeTest = $conn->query($sqlEmployee);
			if($employeeTest->fetch_assoc() === null){
				echo "
					<tr>
						<td>No Logged Hours.</td>
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
				$employeeResults = $conn->query($sqlEmployee);
				while($row = $employeeResults->fetch_assoc()){
					echo "
						<tr>
							<td>".$row['date']."</td>
							<td>".$row['proj_name']."</td>
							<td>".$row['req_name']."</td>
							<td>".$row['req_ana_hours']."</td>
							<td>".$row['design_hours']."</td>
							<td>".$row['coding_hours']."</td>
							<td>".$row['testing_hours']."</td>
							<td>".$row['management_hours']."</td>
						</tr>
					";
				}
			}
			?>
		</tbody>
	</table>
	<a href="manageEmployees.php" class='btn btn-primary' role='button'>Back to Employees</a>
</div>
<?php
require_once('footer.php');
?>