<?php
	require_once '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	require_once "../util/db-config.php";
	require_once "../util/db-query.php";
	require_once "../util/crypto.php";
	use PhpDump\Dump;

	require '../util/send-mail.php';

	$method = $_REQUEST["method"];
	$ids = json_decode($_REQUEST["ids"], true);

	function start_ics($ids) {
		global $key, $cipher;
		$file_name = "";
		$response = ["file_name" => $file_name, "success" => false];
		$first_event = true;
		$file = "";

		foreach ($ids as $key_name => $value) {
			$id = decrypt(urldecode($value), $key, $cipher);	
			if ($first_event) {
				$response["file_name"] = $id . ".ics";
				if (file_exists("../files/" . $response["file_name"])){
					$response["success"] = true;
					$response["file_name"] = encrypt($response["file_name"], $key, $cipher);
					return $response;
				} else {
					$file = fopen("../files/" . $response["file_name"], "x");
					$head_text = "BEGIN:VCALENDAR\n";
					fwrite($file, $head_text);
				}
				$first_event = false;
			}

			$event_details = generat_ics_text($id);

			foreach ($event_details as $key_name => $text){
				fwrite($file, $text);
			}	
		}

		fwrite($file, "END:VCALENDAR\n");
		fclose($file);
		if ($response["file_name"]) {
			$response["file_name"] = encrypt($response["file_name"], $key, $cipher);
			$response["success"] = true;
		}

		return $response;
	}

	function generat_ics_text($id) {
		$query_result = get_event_data($id)->fetch_array(MYSQLI_ASSOC);

		$event_details = [
			"event_begin" => "BEGIN:VEVENT\n",
			"uid" => "UID:$id\n",
			"summary" => "SUMMARY:" . $query_result["event_name"] . "\n",
			"priority" => "PRIORITY:1\n",
			"dt_start" => "DTSTART:" 
			. get_ics_datetime($query_result["start_date"], $query_result["start_time"]) . 
			"\n",
			"dt_end" => "DTEND:" 
			. get_ics_datetime($query_result["start_date"], $query_result["end_time"]) . 
			"\n",
			"location" => "LOCATION:" . $query_result["location"] . "\n"
		];
		

		if (array_key_exists("recur_until", $query_result) && 
			$query_result["recur_until"] != "") {			
			$event_details["r_rule"] = "RRULE:FREQ=" 
			. strtoupper(trim($query_result["frequency"])) . ";"
			. "UNTIL=" . get_ics_datetime($query_result["recur_until"]) . ";"
			. "WKST=SU;";

			$additional_days = $query_result["days_recurrence"];
			$additional_days = ($additional_days ? $additional_days : []);
			if ($additional_days) {
				$additional_days = get_days_abbr($additional_days);
			}

			if (strtoupper(trim($query_result["frequency"])) != "MONTHLY" && 
				strtoupper(trim($query_result["frequency"])) != "YEARLY") {

				$event_details["r_rule"] .= "BYDAY=" . strtoupper(substr(date("l", strtotime($query_result["start_date"])), 0, 2));

				foreach ($additional_days as $value) {
					$value = "," . $value;
					$event_details["r_rule"] .= $value;
				}
			}
			$event_details["r_rule"] .= "\n";
		}

		$event_details["event_end"] = "END:VEVENT\n";

		return $event_details;
	}

	function get_event_data($id) {
		global $servername, $username, $password;
		if ($id) {
			$sql = "
				SELECT * FROM Test
				WHERE id = '" . $id . "';
			";
			
			$result = db_query($sql, $servername, $username, $password, "cal_event");
		} else {
			return false;
		}

		return $result;
	}

	function get_ics_datetime($date, $time = "23:59") {
		$conv_date = date("Ymd", strtotime($date));
		$conv_time = date("His", strtotime($time));

		return $conv_date . "T" . $conv_time;
	}

	function get_days_abbr($days_str) {
		$days_abbr = [];
		$days_str = "**" . $days_str; // strpos returns false - with the ** 0 will not be return.
		// sunday
		if (stripos($days_str, "sun")) {
			array_push($days_abbr, "SU");
		}
		// monday
		if (stripos($days_str, "mon")) {
			array_push($days_abbr, "MO");
		}
		// tuesday
		if (stripos($days_str, "tues")) {
			array_push($days_abbr, "TU");
		}
		// wednesday
		if (stripos($days_str, "wed")) {
			array_push($days_abbr, "WE");
		}
		// thursday
		if (stripos($days_str, "thur")) {
			array_push($days_abbr, "TH");
		}
		// friday
		if (stripos($days_str, "fri")) {
			array_push($days_abbr, "FR");
		}
		// saturday
		if (stripos($days_str, "sat")) {
			array_push($days_abbr, "SA");
		}

		return $days_abbr;
	}



	switch ($method) {
		case "start_ics":
			echo json_encode(start_ics($ids));
			break;

		default:
			break;
	}
?>
