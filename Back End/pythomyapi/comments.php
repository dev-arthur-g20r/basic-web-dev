<?php  
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET");
	header("Content-Type: application/json");

	require_once "connect.php";
	require_once "extensions/stringExtensions.php";

	$result = array();
	

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$forumID = "";
		$isForumIDNull = !isset($_GET["id"]);
		if (!$isForumIDNull) {
			$forumID = $_GET["id"];
		}
		$isForumIDEmpty = $forumID == "";
		$isForumIDNullOrEmpty = $isForumIDNull || $isForumIDEmpty;
		
		if (!$isForumIDNullOrEmpty) {
			$sqlStatement = "SELECT 
				tbl_forumcomments.fld_id,
				tbl_users.fld_firstname,
				tbl_forumcomments.fld_by,
				tbl_users.fld_lastname,
				tbl_users.fld_email,
				tbl_users.fld_username,
				tbl_users.fld_role,
				tbl_forumcomments.fld_comment,
			DATE_FORMAT(tbl_forumcomments.fld_timestamp,'$dateAndTimeFormatToDisplay') AS fld_timestamp 
			FROM tbl_users,tbl_forumcomments 
			WHERE tbl_users.fld_id = tbl_forumcomments.fld_by 
			AND tbl_forumcomments.fld_forumid = '$forumID' 
			ORDER BY DATE(DATE_FORMAT(tbl_forumcomments.fld_timestamp,'%Y-%m-%d %H:%i:%s')) DESC, 
			TIME(DATE_FORMAT(tbl_forumcomments.fld_timestamp,'%Y-%m-%d %H:%i:%s')) DESC";

			$query = $dbConnection->query($sqlStatement);
			$commentsList = [];

			if ($query) {
				header("HTTP/1.1 200 OK");
				while ($data = mysqli_fetch_assoc($query)) {
					$information = array();
					$information["commentID"] = $data["fld_id"];
					$information["comment"] = $data["fld_comment"];
					$creator = array();
					$creator["userID"] = $data["fld_by"];
					$creator["name"] = $data["fld_firstname"]." ".
					$data["fld_lastname"];
					$creator["emailAddress"] = $data["fld_email"];
					$creator["role"] = $data["fld_role"];
					$information["creator"] = $creator;
					$information["dateAndTime"] = $data["fld_timestamp"];
					array_push($commentsList, $information);
				}
				$result = array(
					"statusCode" => 200,
					"message" => "Successfully provided comments!",
					"payload" => $commentsList
				);
			} else {
				header("HTTP/1.1 400 Bad Request");

				$result = array(
					"statusCode" => 400,
					"message" => "Invalid Request: ".mysqli_error($dbConnection)
				);
			}
		} else {
			header("HTTP/1.1 400 Bad Request");

			$result = array(
				"statusCode" => 400,
				"message" => "Invalid Request: Missing `id`."
			);
		}
	} else {
		header("HTTP/1.1 400 Bad Request");

		$result = array(
			"statusCode" => 400,
			"message" => "Invalid request method."
		);
	}

	echo json_encode($result);
?>