<?php 
	define("SERVER", "localhost");
	define("USER", "root");
	define("PASSWORD", "");
	define("DATABASE", "db_pythomy");

	$dbConnection = mysqli_connect(SERVER, USER, PASSWORD, DATABASE);

	if (!$dbConnection) {
		echo "Failed to connect to database!";
	}

	// Always call this file to connect to database.
?>