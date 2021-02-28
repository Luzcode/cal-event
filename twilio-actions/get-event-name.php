<?php
	header('Content-Type: text/plain');
	require '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	use PhpDump\Dump;

	require '../util/send-mail.php';

	$memory = json_decode($_REQUEST["Memory"], true);
	$event_count = $memory["event_count"];
	$event_id = substr($memory["twilio"]["sms"]["From"], 1);
	$event_id .= time();

	$task = [
		"actions" => [
			[
				"remember" => [
					"event_id_$event_count" => $event_id,
					"run_query" => 1
				]
			],
			[
				"collect" => [
					"name" => "collect_event_" . $event_count . "_name",
					"questions" => [
						[
							"question" => "What's the title?",
							"name" => "title"
						]
					],
					"on_complete" => [
						"redirect" => "task://get_location"
					]
				]
			]
		]
	];

	// ------Output to Twilio-------

	echo json_encode($task);
	