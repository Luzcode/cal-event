<?php
	header('Content-Type: text/plain');
	require '../util/no-cache.php';
	require_once '../vendor/autoload.php';
	use PhpDump\Dump;

	require '../util/send-mail.php';

	$memory = json_decode($_REQUEST["Memory"], true);

	// dumpMail($memory);
	
	$event_count = $memory["event_count"];

	$task = [
		"actions" => [
			[
				"say" => "Bye!"
			]
		]
	];

	echo json_encode($task);