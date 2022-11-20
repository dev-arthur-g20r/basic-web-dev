<?php 
	require_once "connect.php";
	require_once "extensions/stringExtensions.php";

	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json");

	$forumsList = [];
	$sqlStatement = "SELECT 
		tbl_forums.fld_id,
		tbl_forums.fld_by,
		tbl_users.fld_lastname,
		tbl_users.fld_firstname,
		tbl_users.fld_email,
		tbl_users.fld_username,
		tbl_users.fld_role,
		tbl_forums.fld_title,
		tbl_forums.fld_body,
		DATE_FORMAT(tbl_forums.fld_timestamp,'$dateAndTimeFormatToDisplay') AS fld_timestamp 
		FROM tbl_users,tbl_forums 
		WHERE tbl_forums.fld_by = tbl_users.fld_id 
		ORDER BY DATE(DATE_FORMAT(tbl_forums.fld_timestamp,'%Y-%m-%d %H:%i:%s')) DESC, 
		TIME(DATE_FORMAT(tbl_forums.fld_timestamp,'%Y-%m-%d %H:%i:%s')) DESC";
	$query = $dbConnection->query($sqlStatement); // Connect query to MySQLi database.

	if ($query) {
		header("HTTP/1.1 200 OK");
		while ($data = mysqli_fetch_assoc($query)) {
			$information = array(); // This is a blank object.
			$information["forumID"] = $data["fld_id"];
			$information["title"] = $data["fld_title"];
			$information["body"] = $data["fld_body"];

			// Nested object
			$creatorData = array();
			$creatorData["userID"] = $data["fld_by"];
			$creatorData["name"] = $data["fld_firstname"]." ".$data["fld_lastname"];
			$creatorData["emailAddress"] = $data["fld_email"];
			$creatorData["username"] = $data["fld_username"];

			$information["creator"] = $creatorData;
			$information["dateAndTime"] = $data["fld_timestamp"];
			array_push($forumsList, $information); // Push mapped information to the forums list/array.
		}
		$result = array(
			"statusCode" => 200,
			"message" => "Success",
			"payload" => $forumsList
		);
	} else {
		header("HTTP/1.1 400 Bad Request");
		$result = array(
			"statusCode" => 400,
			"message" => "Bad Request: ".mysqli_error($dbConnection)
		);
		// mysqli_error($dbConnection) is used to display exact error in SQL query/statement.
	}
	echo json_encode($result);
?>