<?php
require_once('header.php');

$sqlProject = 'select * from project';
$sqlRisks = 'select * from risks';
$sqlRequirements = 'select * from requirements';
$sqlEmployees = 'select * from employees';
$sqlAssignment = 'select * from assignment';
$sqlHours = 'select * from hours_worked';

$projectResults = $conn->query($sqlProject);
$risksResults = $conn->query($sqlRisks);
$requirementsResults = $conn->query($sqlRequirements);
$employeesResults = $conn->query($sqlEmployees);
$assignmentResults = $conn->query($sqlAssignment);
$hoursResults = $conn->query($sqlHours);

echo "
	<div class='container-fluid'>
		<h2>All Data</h2>
		<p>
			This page simply displays all the data in the database. This includes the test data I direcly put in there and the data to be inserted through the app. 
			Obviously this is for testing purposes. This is here so that everyone is able to see the data without needing access to the database management system (as I would have
			to give you my goDaddy account info which I don't want to do). If you want/need to change the way the tables are laid out, let me know and I will do it. Thanks!
		</p>
		
		<h3>Project Table</h3>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>proj_id</th>
					<th>owner_emp_id</th>
					<th>proj_name</th>
					<th>proj_desc</th>
				</tr>
			</thead>
		";
			while($row = $projectResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row['proj_id']."</td>
						<td>".$row['owner_emp_id']."</td>
						<td>".$row['proj_name']."</td>
						<td>".$row['proj_desc']."</td>
					</tr>
				";
			}
			
			echo "
		</table>
		
		<h3>Risks Table</h3>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>risk_id</th>
					<th>proj_id</th>
					<th>risk_name</th>
					<th>risk_desc</th>
					<th>risk_status</th>
				</tr>
			</thead>
		";
			while($row = $risksResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row['risk_id']."</td>
						<td>".$row['proj_id']."</td>
						<td>".$row['risk_name']."</td>
						<td>".$row['risk_desc']."</td>
						<td>".$row['risk_status']."</td>
					</tr>
				";
			}
			
			echo "
		</table>
		
		<h3>Employees Table</h3>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>emp_id</th>
					<th>emp_fname</th>
					<th>emp_lname</th>
				</tr>
			</thead>
		";
			while($row = $employeesResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row['emp_id']."</td>
						<td>".$row['emp_fname']."</td>
						<td>".$row['emp_lname']."</td>
					</tr>
				";
			}
			
			echo "
		</table>
		
		<h3>Requirements Table</h3>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>req_id</th>
					<th>proj_id</th>
					<th>req_name</th>
					<th>req_desc</th>
				</tr>
			</thead>
		";
			while($row = $requirementsResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row['req_id']."</td>
						<td>".$row['proj_id']."</td>
						<td>".$row['req_name']."</td>
						<td>".$row['req_desc']."</td>
					</tr>
				";
			}
			
			echo "
		</table>
		
		<h3>Assignments Table</h3>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>assign_id</th>
					<th>req_id</th>
					<th>emp_id</th>
					<th>proj_id</th>
				</tr>
			</thead>
		";
			while($row = $assignmentResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row['assign_id']."</td>
						<td>".$row['req_id']."</td>
						<td>".$row['emp_id']."</td>
						<td>".$row['proj_id']."</td>
					</tr>
				";
			}
			
			echo "
		</table>
		
		<h3>Hours_Worked Table</h3>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>assign_id</th>
					<th>req_id</th>
					<th>emp_id</th>
					<th>proj_id</th>
					<th>date</th>
					<th>req_ana_hours</th>
					<th>design_hours</th>
					<th>coding_hours</th>
					<th>testing_hours</th>
					<th>management_hours</th>
				</tr>
			</thead>
		";
			while($row = $hoursResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row['assign_id']."</td>
						<td>".$row['req_id']."</td>
						<td>".$row['emp_id']."</td>
						<td>".$row['proj_id']."</td>
						<td>".$row['date']."</td>
						<td>".$row['req_ana_hours']."</td>
						<td>".$row['design_hours']."</td>
						<td>".$row['coding_hours']."</td>
						<td>".$row['testing_hours']."</td>
						<td>".$row['management_hours']."</td>
					</tr>
				";
			}
			
			echo "
		</table>		
	</div>";
	

require_once('footer.php');
?>