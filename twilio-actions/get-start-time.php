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
					"name" => "collect_start_time_" . $event_count,
					"questions" => [
						[
							"question" => "Start time?",
							"name" => "start_time",
							"type" => "Twilio.TIME"
						]
					],
					"on_complete" => [
						"redirect" => "task://get_end_time"
					]
				]
			]
		]
	];

	// -------Output to Twilio----------

	echo json_encode($task);