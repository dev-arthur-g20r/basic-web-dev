<?php 
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST"); // This header documents the allowed request method in Postman.
	header("Content-Type: application/json");

	require_once "connect.php";

	$result = array();
	$data = json_decode(file_get_contents("php://input"));

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$isIDNull = !isset($data->id);
		$id = "";
		if (!$isIDNull) {
			$id = $data->id;
		}
		$isIDBlank = $id == "";
		$isIDNullOrEmpty = $isIDNull || $isIDBlank;
		
		// Prioritize checking the ID of the forum. It is the most required param.
		if (!$isIDNullOrEmpty) {
			$isTitleNull = !isset($data->title);
			$isBodyNull = !isset($data->body);
			$title = "";
			$body = "";
			if (!$isTitleNull) {
				$title = $data->title;
			}
			if (!$isBodyNull) {
				$body = $data->body;
			}
			$isTitleEmpty = $title == "";
			$isBodyEmpty = $title == "";
			$isTitleNullOrEmpty = $isTitleNull || $isTitleEmpty;
			$isBodyNullOrEmpty = $isBodyNull || $isBodyEmpty;
			$isForumNullOrEmpty = $isTitleNullOrEmpty || $isBodyNullOrEmpty;
			if (!$isForumNullOrEmpty) {
				$sqlStatement = "UPDATE tbl_forums SET fld_title = '$title', 
					fld_body = '$body' WHERE fld_id = '$id'";
				$query = $dbConnection->query($sqlStatement);
				if ($query) {
					header("HTTP/1.1 202 Accepted");
					$result = array(
						"statusCode" => 202,
						"message" => "Forum successfully updated!"
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
					"message" => "Invalid Request: Missing `title` or `body`."
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