<?php
	header('Content-Type: text/plain');
	require '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	use PhpDump\Dump;

	require '../util/send-mail.php';

	$memory = json_decode($_REQUEST["Memory"], true);
	
	$event_count = $memory["event_count"];

	$ask_days_recurrence = [
		"actions" => [
			[
				"collect" => [
					"name" => "ask_days_recurrence_" . $event_count,
					"questions" => [
						[
							"question" => "Does the event occur on multiple days?",
							"name" => "days_recurrence_consent",
							"type" => "Twilio.YES_NO"
						]
					],
					"on_complete" => [
						"redirect" => "task://ask_days_recurrence"
					]
				]
			]
		]
	];

	$get_days_recurrence = [
		"actions" => [
			[
				"collect" => [
					"name" => "get_days_recurrence_" . $event_count,
					"questions" => [
						[
							"question" => "What additional days (eg. Tuesday, Friday; weekdays; weekends; everyday)?",
							"name" => "days_recurrence"
						]
					],
					"on_complete" => [
						"redirect" => "task://ask_days_recurrence"
					]
				]
			]
		]
	];

	$redirect_ask_frequency = [
		"actions" => [
			[
				"redirect" => "task://ask_frequency"
			]
		]
	];

	// -------Output to Twilio--------

	if (array_key_exists("ask_days_recurrence_" . $event_count, $memory["twilio"]
		["collected_data"])) {
		
		$days_recurring = $memory["twilio"]["collected_data"]
		["ask_days_recurrence_" . $event_count]["answers"]["days_recurrence_consent"]
		["answer"];
		
		if (strcasecmp($days_recurring, "yes") == 0) {

			if (array_key_exists("get_days_recurrence_" . $event_count,
				$memory["twilio"]["collected_data"])) {
				// get frequency
				echo json_encode($redirect_ask_frequency);
			} else {
				// get days recurrence
				echo json_encode($get_days_recurrence);
			}

		} else {
			// get frequency
			echo json_encode($redirect_ask_frequency);
		}
	} else {
		// ask days recurrence
		echo json_encode($ask_days_recurrence);
	}


