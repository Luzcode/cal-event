<?php
	header('Content-Type: text/plain');
	require '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	require_once "../util/db-config.php";
	require_once "../util/crypto.php";

	use PhpDump\Dump;

	require '../util/send-mail.php';

	$memory = json_decode($_REQUEST["Memory"], true);
	$event_count = $memory["event_count"];
	
	if ($memory["run_query"] == 1) {
		$current_event_id = $memory["event_id_$event_count"];
		$event_name = $memory["twilio"]["collected_data"]
		["collect_event_" . $event_count ."_name"]["answers"]
		["title"]["answer"];
		$location = $memory["twilio"]["collected_data"]
		["collect_location_$event_count"]["answers"]
		["location"]["answer"];
		$start_time = $memory["twilio"]["collected_data"]
		["collect_start_time_$event_count"]["answers"]
		["start_time"]["answer"];
		$end_time = $memory["twilio"]["collected_data"]
		["collect_end_time_$event_count"]["answers"]
		["end_time"]["answer"];
		$start_date = $memory["twilio"]["collected_data"]
		["collect_date_$event_count"]["answers"]
		["date"]["answer"];
		$days_recurrence = "";
		$frequency = "";
		$recur_until = "";
		
		// Optional Fields
		if (array_key_exists("get_days_recurrence_$event_count", 
		$memory["twilio"]["collected_data"])){
			$days_recurrence = $memory["twilio"]["collected_data"]
			["get_days_recurrence_$event_count"]["answers"]
			["days_recurrence"]["answer"];
		}
		if (array_key_exists("get_frequency_$event_count", 
		$memory["twilio"]["collected_data"])){		
			$frequency = $memory["twilio"]["collected_data"]
			["get_frequency_$event_count"]["answers"]
			["frequency"]["answer"];

			$recur_until = $memory["twilio"]["collected_data"]
			["recur_until_$event_count"]["answers"]
			["until"]["answer"];
		}


		// Add event details to database

		// query template
		$temp_query = "
			INSERT INTO Test
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
		";

		// create new connection
		$db_conn = new mysqli($servername, $username, $password, "cal_event");
		// check connection success
		if ($db_conn->connect_error) {
			die("Connection failed: " . $db_conn->connect_error);
		}

		// prep statement
		$query_stmt = $db_conn->prepare($temp_query);
		
		// bind and execute
		if ($query_stmt) {
			$query_stmt->bind_param("sssssssss", $current_event_id, $event_name,
			$location, $start_time, $end_time, $start_date, $days_recurrence,
			$frequency, $recur_until);

			if (!$query_stmt->execute()) {
				echo "Execution error: " . $db_conn->error;
			}
		} else {
			echo "Preparation error: " . $db_conn->error;
		}

	}
	

	// Tasks

	$ask_another_event = [
		"actions" => [
			[
				"remember" => [
					"run_query" => 0
				]
			],
			[
				"collect" => [
					"name" => "next_event_" . $event_count,
					"questions" => [
						[
							"question" => "Wanna add another event?",
							"name" => "another_event",
							"type" => "Twilio.YES_NO"
						]
					],
					"on_complete" => [
						"redirect" => "task://give_link"
					]
				]
			]
		]
	];

	$rem_redirect = [
		"actions" => [
			[
				"remember" => [
					"event_count" => $event_count + 1
				]
			],
			[
				"redirect" => "task://get_event_name"
			]
		]
	];

	$give_link = [
		"actions" => [
			[
				"show" => [
					"body" => "Click on this link to generate a calendar format (ical): https://luzcode.com/cal-event/ical/?"
				]
			],
			[
				"redirect" => "task://goodbye"
			]
		]
	];

	// -------Output to Twilio-------

	if (array_key_exists("next_event_" . $event_count, $memory["twilio"]
		["collected_data"])) {
		
		$another_event = $memory["twilio"]["collected_data"]
		["next_event_" . $event_count]["answers"]["another_event"]["answer"];
		
		if (strcasecmp($another_event, "yes") == 0) {
			// increase event count and link back to event name
			echo json_encode($rem_redirect);
		} else {
			// give link	
			// Encode
			for ($i = 0; $i < $event_count; ++$i) {
				$give_link["actions"][0]["show"]["body"] .= $i ? "&" : "";
				$id_number = $i + 1; // Stored id with numbering starting from 1.
				$event_id = encrypt($memory["event_id_$id_number"], $key, $cipher);
				$give_link["actions"][0]["show"]["body"] .= "id$id_number=" . urlencode($event_id);
			}
			echo json_encode($give_link);
		}
	} else {
		// ask-another-event
		echo json_encode($ask_another_event);
	}
	