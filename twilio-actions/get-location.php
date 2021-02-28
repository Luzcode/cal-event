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
					"name" => "collect_location_" . $event_count,
					"questions" => [
						[
							"question" => "Where is it gonna take place?",
							"name" => "location"
						]
					],
					"on_complete" => [
						"redirect" => "task://get_date"
					]
				]
			]
		]
	];

	// -------Output to Twilio-------

	echo json_encode($task);