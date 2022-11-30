<?php 
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json");

	require_once "connect.php";

	$data = json_decode(file_get_contents("php://input"));
	$result = array();

	// Only allow POST as the request method.
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$forumID = "";
		$isForumIDNull = !isset($data->forumID);
		if (!$isForumIDNull) {
			$forumID = $data->forumID;
		}
		$isForumIDEmpty = $forumID == "";
		$isForumIDNullOrEmpty = $isForumIDNull || $isForumIDEmpty;

		if (!$isForumIDNullOrEmpty) {
			$sqlStatement = "DELETE FROM tbl_forums WHERE fld_id = '$forumID'";
			$query = $dbConnection->query($sqlStatement);
			if ($query) {
				header("HTTP/1.1 202 Created");

				$result = array(
					"statusCode" => 202,
					"message" => "Forum successfully deleted!"
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
				"message" => "Invalid Request: Missing `forumID`."
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