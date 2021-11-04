<?php
	$actions = array(
		'app' => array(
			'cities' => array(
				'read' => array(
					'check' => false,
					'get' => function($query, &$state) {
						if((isset($query['city'] && is_string($query['city'])) && (isset($query['date']) && isset($functions['dateCheck']($query['date'])))) {
							//check by y-m-d in BBDD and city
							return array('code' => '200', $data => 'json of weather');
						}
						else {
							$state = '480 Invalid data';
							return false;
						}
					},
					'set' => false,
					'log' => false,
					'cache' => false
				),
				'create' => array(
					'check' => false,
					'get' => false,
					'set' => function($query, &$state) {
						$salida = true;
						if(isset($query['city'] && is_string($query['city'])) && (isset($query['info'] && is_array($query['info']))) {
							foreach($query['info'] as $key => $date) {
								if(is_string($date)) {
									try {
										// call to api
									} catch (\Throwable $th) {
										$state = '492 error inserting this date: ' . $date;
										$salida = false;
									}
								}else{
									$state = '480 Invalid data';
									$salida = false;
								}
							}
						}
						else {
							$state = '480 Invalid data';
							$salida = false;
						}
						if($salida) {
							return array('code' => '200');
						}else {
							$return false;
						}
					},
					'log' => false,
					'cache' => false
				)
			)
		)
	);
?>
