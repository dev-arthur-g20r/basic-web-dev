<?php  
	header('Access-Control-Allow-Origin: *');
	header('content-type:application/json');

	date_default_timezone_set("Asia/Manila");
	$date = date("Y-m-d H:i:s", time()); // Get current date and time.

	// Convert JSON string (request body) to JSON object.
	$data = json_decode(file_get_contents("php://input"));

	require_once('connect.php');

	$nullTitle = !isset($data->title);
	$nullBody = !isset($data->body);
	$title = "";
	$body = "";
	$result = array();

	if (!$nullTitle && !$nullBody) {
		$title = mysqli_real_escape_string($dbConnection, $data->title);
		$body = mysqli_real_escape_string($dbConnection, $data->body);
	}
	$blankTitle = $title == "";
	$blankBody = $body == "";
    
	$by = "1278"; // Static user ID for now for testing purposes.
	$sql = "INSERT INTO tbl_forums(
		fld_title,
		fld_body,
		fld_by,
		fld_timestamp
	) VALUES(
	'$title',
	'$body',
	'$by',
	'$date')";

	if (!$nullTitle && 
		!$nullBody &&
		!$blankTitle && 
		!$blankBody
	) {
		$query = $dbConnection->query($sql);
		if ($query) {
			header("HTTP/1.1 201 Created");
			$result = array(
				"statusCode" => 201,
				"message" => "Forum added!"
			);
		} else {
			header("HTTP/1.1 403 Forbidden");
			$result = array(
				"statusCode" => 403,
				"message" => "Forum not accepted!"
			);
		}
	} else {
		$result = array(
			"statusCode" => 403,
			"message" => "Forum not accepted!"
		);
	}
	echo json_encode($result);
	
?>