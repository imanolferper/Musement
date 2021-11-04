<?php
	$functions = array(
		'defaultGet' => function($query, $state) {
			global $functions, $PROJECT;
			// for global functions like logins...
		}
		'checkDate' => function ($date, $format = 'Y-m-d') {
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) === $date;
		}
	);
?>