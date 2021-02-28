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
					"name" => "collect_date_" . $event_count,
					"questions" => [
						[
							"question" => "What date or day? (the first date/day if there are multiple days)",
							"name" => "date",
							"type" => "Twilio.DATE"
						]
					],
					"on_complete" => [
						"redirect" => "task://get_start_time"
					]
				]
			]
		]
	];

	// ------Output to Twilio

	echo json_encode($task);