<?php
require_once('header.php');

if(isset($_POST['delete'])){
	$proj_id = $_POST['delete'];
	$sqlDelete = "DELETE FROM project WHERE proj_id = '$proj_id'";
	if($conn->query($sqlDelete) === true){
		echo "<script type='text/javascript'>alert('Project deleted successfully!')</script>";
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$conn->error."')</script>";
	}
}

if(isset($_POST['submit'])){
	
	//The following takes the POST data and 'filters' it before inserting into the database to prevent SQL injection attacks. 
	//All user input should be 'filtered' before inserting into the database.
	
	$projectName = strip_tags($_POST['projectName']);
	$projectDescription = strip_tags($_POST['projectDescription']);
			
	$projectName = stripslashes($projectName);
	$projectDescription = stripslashes($projectDescription);

    $projectName = mysqli_real_escape_string($conn, $projectName);
    $projectDescription = mysqli_real_escape_string($conn, $projectDescription);
	list($projectOwner, $ownerId) = explode("-" , $_POST['owner'], 2);

	$sqlInsert = $conn->prepare("INSERT INTO project(proj_name, proj_desc, owner_emp_id) VALUES(?, ?, ?)");
	$sqlInsert->bind_param('ssi', $projectName, $projectDescription, $ownerId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Success! The project named ".$projectName." owned by ".$projectOwner." has been added to the database!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

$sqlProjects = "
	SELECT proj_name, proj_desc, proj_id, emp_fname, emp_lname 
	FROM project
	INNER JOIN employees ON project.owner_emp_id = employees.emp_id";
$sqlName = "SELECT * FROM employees";

$projectResults = $conn->query($sqlProjects);
$nameResults = $conn->query($sqlName);
?>

<div class='container-fluid'>
	<h3>Manage Projects</h3>
	<div class='row'>
		<div class='col-sm'>
			<div class='alert alert-primary'>
				Note: A project must not have any requirements to be deleted.
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#addProj">Add a Project</button>
	<div id="addProj" class="collapse">
		<form action='manageProjects.php' method='post' enctype='multipart/form-data'>
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<label for='projectName'>Project Name:</label>
						<input type='text' class='form-control' name='projectName' id='projectName' required>
					</div>
				</div>
			</div>
			
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						<label for='owner'>Select Project Owner:</label>
						<select class='form-control' id='owner' name='owner'>
					
						<?php
						while($row = $nameResults->fetch_assoc()){
							$ownerName = $row['emp_fname']." ".$row['emp_lname'];
							$ownerId = $row['emp_id'];
							echo "
								<option value='".$ownerName."-".$ownerId."'>".$ownerName."</option>
							";
						}
						?>
						</select>
					</div>
				</div>
			</div>

			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						<label for='projectDescription'>Project Description:</label>
						<textarea class='form-control' rows='5' name='projectDescription' id='projectDescription'  required></textarea>
					</div>
				</div>
			</div>
			
			<input name='submit' type='submit' value='Submit'>
		</form>
	</div>
	
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Project Name</th>
				<th>Project Owner</th>
				<th>Project Description</th>
				<th>Manage</th>
				<th>Delete Project</th>
			</tr>
		</thead>
		<tbody>
			<?php
			while($row = $projectResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row['proj_name']."</td>
						<td>".$row['emp_fname']." ".$row['emp_lname']."</td>
						<td>".$row['proj_desc']."</td>
						<td>
							<form action='manageProject.php' method='post'>
								<button type='submit' class='btn btn-primary' name ='manage' value='".$row['proj_id']."'>Manage</button>
							</form>
						</td>
						<td>
							<form action='manageProjects.php' method='post'>
								<button type='submit' class='btn btn-danger' name ='delete' value='".$row['proj_id']."'>Delete</button>
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