<?php

	$_SERVER['REQUEST_METHOD'] = 'GET';
	$_SERVER['HTTP_USER_AGENT'] = 'Terminal';
	parse_str(isset($argv[1]) ? $argv[1] : '', $_GET);

	include('api.php');
?>