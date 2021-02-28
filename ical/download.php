<?php
	require_once "../util/no-cache.php";
	require_once "../util/crypto.php";
	require_once "../vendor/autoload.php";
	require "../util/send-mail.php";
	
	if (array_key_exists("fn", $_REQUEST) && $_REQUEST["fn"] != "") {
		$file_name = "../files/" . 
		decrypt($_REQUEST["fn"], $key, $cipher);
		$content_type = mime_content_type($file_name);
		header("Content-Type: $content_type");
		header("Content-Disposition: attachment; filename=ical.ics;");
		readfile($file_name);
		exit;
	} else {
		echo "<script>alert('Couldn\'t find ics file.');</script>";
		exit;
	}

?>