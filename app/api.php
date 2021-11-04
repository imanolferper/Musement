<?php
	header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
	header('Access-Control-Allow-Credentials: true');
	if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		exit(0);
	}
	header('Access-Control-Allow-Methods: *');

	$methods = array(
		'POST' => 'create',
		'GET' => 'read',
		'DELETE' => 'delete',
		'PUT' => 'modify'
	);
	$method = $_SERVER['REQUEST_METHOD'];
	if(($method == 'POST') && isset($_SERVER['HTTP_X_HTTP_METHOD'])) {
		if($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') $method = 'DELETE';
		elseif($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') $method = 'PUT';
	}
	$states = array(
		200 => 'OK',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		410 => 'Gone',
		412 => 'Precondition Failed',
		500 => 'Internal Server Error'
	);

	switch($method) {
		case 'POST':
			$QUERY = $_POST;
			break;
		case 'PUT':
		case 'DELETE':
			parse_str(file_get_contents("php://input"), $QUERY);
			break;
		default:
			$QUERY = $_GET;
			break;
	}

	if($_SERVER['CONTENT_TYPE'] == 'application/json') {
		$QUERY = ($content_json = file_get_contents("php://input")) && ($content = json_decode($content_json, true)) ? $content : array();
	}
	$state = 200;
	$cache = false;
	$response = '{}';
	$actions = array();
	$log = array(
		'active' => false
	);

	$scope = $_GET['__scope__'];
	if(isset($QUERY['__scope__'])) {
		unset($QUERY['__scope__']);
	}
	$collection = $_GET['__collection__'];
	if(isset($QUERY['__collection__'])) {
		unset($QUERY['__collection__']);
	}
	$action = $_GET['__action__'];
	if(isset($QUERY['__action__'])) {
		unset($QUERY['__action__']);
	}
	if($_GET['__content_id_1__'] != '') {
		$QUERY['_content_id_1'] = $_GET['__content_id_1__'];
	}
	if(isset($QUERY['__content_id_1__'])) {
		unset($QUERY['__content_id_1__']);
	}
	if($_GET['__content_id_2__'] != '') {
		$QUERY['_content_id_2'] = $_GET['__content_id_2__'];
	}
	if(isset($QUERY['__content_id_2__'])) {
		unset($QUERY['__content_id_2__']);
	}
	if($_GET['__content_id_3__'] != '') {
		$QUERY['_content_id_3'] = $_GET['__content_id_3__'];
	}
	if(isset($QUERY['__content_id_3__'])) {
		unset($QUERY['__content_id_3__']);
	}
	$debug = (isset($QUERY['__debug__']) && $QUERY['__debug__']) ? true : false;
	if(isset($QUERY['__debug__'])) {
		unset($QUERY['__debug__']);
	}

	define('API_SCOPE', $scope);
	define('API_COLLECTION', $collection);
	define('API_ACTION', $action);
	define('API_METHOD', ($method != '') && isset($methods[$method]) ? $methods[$method] : false);

	$log_core_inicio = microtime(true);

	if($debug) {
		echo 'API::/' . API_SCOPE . '/' . API_COLLECTION . '/' . API_ACTION . '/' . API_METHOD . "\n";
	}

	$query_cache = $QUERY;
	unset($query_cache['_c']);
	unset($query_cache['__debug__']);

	if(($scope != '') && is_dir(dirname(__FILE__) . '/' . $scope) && (dirname(__FILE__) != realpath(dirname(__FILE__) . '/' . $scope)) && is_file(dirname(__FILE__) . '/' . $scope . '/api.php')) {
		include(dirname(__FILE__) . '/' . $scope . '/api.php');
	}
	if(($state == 200) && !API_METHOD) {
		$state = 405;
	}
	elseif(($state == 200) && ((API_COLLECTION == '') || (API_ACTION == '') || (API_METHOD == '') || !isset($actions[API_COLLECTION][API_ACTION][API_METHOD])) || !($query_action = $actions[API_COLLECTION][API_ACTION][API_METHOD])) {
		$state = 404;
	}
	elseif(($state == 200) && isset($query_action['cache']) && $query_action['cache'] && isset($query_action['cache']['get']) && ($response_cache = $query_action['cache']['get']($query_cache))) {
		$response = $response_cache;
	}
	else {
		if(($state == 200) && isset($query_action['check']) && $query_action['check'] && !$query_action['check']($QUERY, $state)) {
			$state = ($state == 200) ? 401 : $state;
		}
		elseif(($state == 200) && ($log['active'] = true) && isset($query_action['get']) && $query_action['get'] && ((($result = $query_action['get']($QUERY, $state)) === false) || !is_array($result) || (($response = json_encode($result)) === false))) {
			$state = ($state == 200) ? 500 : $state;
		}
		elseif(($state == 200) && isset($query_action['set']) && $query_action['set'] && (!$query_action['set']($result, $state) || !is_array($result) || (($response = json_encode($result)) === false))) {
			$state = ($state == 200) ? 500 : $state;
		}
		if(($state == 200) && isset($query_action['cache']) && $query_action['cache'] && isset($query_action['cache']['set'])) {
			$query_action['cache']['set']($response, $query_cache);
		}
	}
	if($state == 200) {
		if($debug) {
			echo 'QUERY::' . json_encode($QUERY) . "\n";
			echo 'CACHE::' . ((isset($response_cache) && $response_cache) ? 'TRUE' : 'FALSE') . "\n";
			echo 'RESPONSE::' . $response . "\n";
		}
	}

	file_put_contents('./logs/' . date('Ymd') . '.log', date('Ymd H:i:s') . '::' . $_SERVER['HTTP_USER_AGENT'] . '::' . '/' . API_SCOPE . '/' . API_COLLECTION . '/' . API_ACTION . '/' . API_METHOD . '::' . json_encode($QUERY) . '::' . $state . '::' . ((isset($response_cache) && $response_cache) ? 'CACHE' : 'DINAMIC') . '::' . (microtime(true) - $log_core_inicio) . "\n", FILE_APPEND);

	if($log['active'] && isset($query_action['log']) && $query_action['log']) {
		$log['api'] = '/' . API_SCOPE . '/' . API_COLLECTION . '/' . API_ACTION . '/' . API_METHOD;
		if($QUERY['_content_id_1'] != '') {
			$log['api'] .= '/' . $QUERY['_content_id_1'];
			if($QUERY['_content_id_2'] != '') {
				$log['api'] .= '/' . $QUERY['_content_id_2'];
				if($QUERY['_content_id_3'] != '') {
					$log['api'] .= '/' . $QUERY['_content_id_3'];
				}
			}
		}
		$log['response'] = $response;
		if(preg_match('/^([0-9]{3})( .*)?$/', $state, $coincidencias)) {
			$log['state'] = $coincidencias[1];
			if($log['response'] == '') {
				$log['response'] = $coincidencias[2];
			}
		}
		else {
			$log['state'] = '412';
			if($log['response'] == '') {
				$log['response'] = $state;
			}
		}
		$log['data'] = $QUERY;
		if($debug) {
			echo 'LOG::' . json_encode($log) . "\n";
		}
		$query_action['log']($log['api'], $log['state'], $log['data'], $log['response']);
	}

	if(isset($states[$state])) {
		header('HTTP/1.1 ' . $state . ' ' . $states[$state]);
	}
	elseif(preg_match('/^[0-9]{3}( .*)?$/', $state)) {
		header('HTTP/1.1 ' . utf8_decode($state));
	}
	else {
		header('HTTP/1.1 412 ' . utf8_decode($state));
	}
	if($state != 200) {
		echo isset($states[$state]) ? $states[$state] : $state;
	}
	else {
		header('Content-type: application/json');
		echo $response;
	}
?>