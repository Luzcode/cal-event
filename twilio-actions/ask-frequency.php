<?php
	header('Content-Type: text/plain');
	require '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	use PhpDump\Dump;

	require '../util/send-mail.php';

	$memory = json_decode($_REQUEST["Memory"], true);
	
	$event_count = $memory["event_count"];

	$ask_frequency = [
		"actions" => [
			[
				"collect" => [
					"name" => "ask_frequency_" . $event_count,
					"questions" => [
						[
							"question" => "Is there any frequency, like daily, weekly, etc? (yes/no)",
							"name" => "frequency_consent",
							"type" => "Twilio.YES_NO"
						]
					],
					"on_complete" => [
						"redirect" => "task://ask_frequency"
					]
				]
			]
		]
	];

	$get_frequency = [
		"actions" => [
			[
				"collect" => [
					"name" => "get_frequency_" . $event_count,
					"questions" => [
						[
							"question" => "What's the frequency(weekly, monthly, etc)?",
							"name" => "frequency",
							"type" => "frequency"
						]
					],
					"on_complete" => [
						"redirect" => "task://ask_frequency"
					]
				]
			]
		]
	];

	$recur_until = [
		"actions" => [
			[
				"collect" => [
					"name" => "recur_until_" . $event_count,
					"questions" => [
						[
							"question" => "On what date does the frequency end?",
							"name" => "until",
							"type" => "Twilio.DATE"
						]
					],
					"on_complete" => [
						"redirect" => "task://give_link"
					]
				]
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

	if (array_key_exists("ask_frequency_" . $event_count, 
	$memory["twilio"]["collected_data"])) {
		
		$frequency_consent = $memory["twilio"]["collected_data"]
		["ask_frequency_" . $event_count]["answers"]["frequency_consent"]
		["answer"];

		if (strcasecmp($frequency_consent, "yes") == 0) {
			if (array_key_exists("get_frequency_" . $event_count,
			$memory["twilio"]["collected_data"])) {
				// get frequency end date
				echo json_encode($recur_until);
			} else {
				// get frequency
				echo json_encode($get_frequency);
			}
		} else {
			// redirect to give-link
			echo json_encode($redirect_give_link);
		}

	} else {
		// get frequency
		echo json_encode($ask_frequency);
	}

