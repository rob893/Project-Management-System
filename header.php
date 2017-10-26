<?php
ini_set('display_errors', false);
require_once('dbconnection.php');
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
		<title>SWE6633 Group Project</title>
	</head>
	<body>
		<div class="jumbotron">
			<h1>Project Management System</h1>
			<a href="index.php" class='btn btn-info' role='button'>Home</a>
			<a href="manageProjects.php" class='btn btn-info' role='button'>Manage Projects</a>
			<a href="manageEmployees.php" class='btn btn-info' role='button'>Manage Employees</a>
			<a href="logHours.php" class='btn btn-info' role='button'>Log Hours</a>
			<br>
			<a href='allData.php'>Click here to see all tables and data currently in the database.</a>
		</div>
