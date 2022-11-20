<?php
	$dateAndTimeFormatToDisplay = "%M %d, %Y %h:%i %p";

	function utf8EncodeData($dictionary) {
		$information = null;
		foreach($dictionary as $key => $value){
			$information[$key] = utf8_encode($value);
		}
		return $information;
	}
?>