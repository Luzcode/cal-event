<?php
	header('Content-Type: text/plain');
	require '../includes/no-cache.php';
	require_once '../vendor/autoload.php';
	use PhpDump\Dump;

	// Get teams notification of new event creation
	$header = ["Content-Type" => "application/x-www-form-urlencoded"];

	$formFields = ["new-event" => 1];

	$handle = curl_init ("https://labs.luzcode.com/ms/teams/bot-message.php");
	curl_setopt ($handle, CURLOPT_HTTPHEADER, $header);
	curl_setopt ($handle, CURLOPT_POST, true);
	curl_setopt ($handle, CURLOPT_POSTFIELDS, http_build_query ($formFields));
	curl_setopt ($handle, CURLOPT_RETURNTRANSFER, true);

	$httpResponse = json_decode (curl_exec ($handle), true);

	curl_close ($handle);

	// Twilio Task
	$task = [
		"actions" => [
			[
				"remember" => [
					"event_count" => 1
				]
			],
			[
				"say" => "Hello, please answer these questions to create your event."
			],
			[
				"redirect" => "task://get_event_name"
			]
		]
	];

	// -----Output to Twilio-------
	echo json_encode($task);
	