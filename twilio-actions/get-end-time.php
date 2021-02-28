<?php
	header('Content-Type: text/plain');
	require '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	use PhpDump\Dump;

	require '../util/send-mail.php';

	$memory = json_decode($_REQUEST["Memory"], true);
	
	$event_count = $memory["event_count"];

	$task = [
		"actions" => [
			[
				"collect" => [
					"name" => "collect_end_time_" . $event_count,
					"questions" => [
						[
							"question" => "End time?",
							"name" => "end_time",
							"type" => "Twilio.TIME"
						]
					],
					"on_complete" => [
						"redirect" => "task://ask_recurrence"
					]
				]
			]
		]
	];

	// -------Output to Twilio--------

	echo json_encode($task);