<?php

	function db_query($sql, $servername, $username, $password, $db_name="") {
		// create connection
		if (strlen($db_name)) {
			$db_conn = new mysqli($servername, $username, $password, $db_name);
		} else {
			$db_conn = new mysqli($servername, $username, $password);
		}

		// check connection success
		if ($db_conn->connect_error) {
			die("Connection failed: " . $db_conn->connect_error);
		}

		$dbc_result = $db_conn->query($sql);

		if ($db_conn->error) {
			echo "Error: " . $db_conn->error;
		}

		// close connection
		$db_conn->close();

		return $dbc_result;
	}