<?php
	header('Content-Type: text/plain');
	require '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	use PhpDump\Dump;

	require '../util/send-mail.php';

	$memory = json_decode($_REQUEST["Memory"], true);
	
	$event_count = $memory["event_count"];

	$ask_recurrence = [
		"actions" => [
			[
				"collect" => [
					"name" => "ask_recurrence_" . $event_count,
					"questions" => [
						[
							"question" => "Is this a recurring event?",
							"name" => "recurrence_consent",
							"type" => "Twilio.YES_NO"
						]
					],
					"on_complete" => [
						"redirect" => "task://ask_recurrence"
					]
				]
			]
		]
	];

	$redirect_ask_days_recurrence = [
		"actions" => [
			[
				"redirect" => "task://ask_days_recurrence"
			]
		]
	];

	$redirect_give_link = [
		"actions" => [
			[
				"redirect" => "task://give_link"
			]
		]
	];

	// -------Output to Twilio--------

	if (array_key_exists("ask_recurrence_" . $event_count, $memory["twilio"]
		["collected_data"])) {

		$recurring = $memory["twilio"]["collected_data"]
		["ask_recurrence_" . $event_count]["answers"]["recurrence_consent"]["answer"];

		if (strcasecmp($recurring, "yes") == 0) {
			// ask days recurrence
			echo json_encode($redirect_ask_days_recurrence);
		} else {
			// redirect to give-link
			echo json_encode($redirect_give_link);
		}	

	} else {
		// ask-recurrence
		echo json_encode($ask_recurrence);
	}


