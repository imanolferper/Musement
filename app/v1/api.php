<?php
	include(dirname(__FILE__) . '/config.php');
	include(dirname(__FILE__) . '/functions.php');

	$headers = isset($_SERVER['HTTP_HOST']) ? apache_request_headers() : array();

	$PROJECT = array();
	$TOKEN = array();

	include(dirname(__FILE__) . '/actions.php');

	foreach($actions as $_collection => $collection_actions) {
		foreach($collection_actions as $_action => $action_methods) {
			foreach($action_methods as $_method => $method_functions) {
				if(
					!isset($method_functions['cache']) &&
					$PROJECT['project'] && isset(PROJECTS[$PROJECT['project']]) && isset(PROJECTS[$PROJECT['project']]['cache'])
				) {
					$actions[$_collection][$_action][$_method]['cache'] = PROJECTS[$PROJECT['project']]['cache'];
				}
				if(isset($actions[$_collection][$_action][$_method]['cache']) && $actions[$_collection][$_action][$_method]['cache']) {
					$cache_time = false;
					$actions[$_collection][$_action][$_method]['cache'] = false;
					if(isset($method_functions['cache']) && isset($method_functions['cache']['time'])) {
						$cache_time = $method_functions['cache']['time'];
					}
					elseif($PROJECT['project'] && isset(PROJECTS[$PROJECT['project']]) && isset(PROJECTS[$PROJECT['project']]['cache']) && isset(PROJECTS[$PROJECT['project']]['cache']['time'])) {
						$cache_time = PROJECTS[$PROJECT['project']]['cache']['time'];
					}
					if($cache_time && is_numeric($cache_time)) {
						$cache_prefix = false;
						if(isset($method_functions['cache']) && isset($method_functions['cache']['prefix'])) {
							$cache_prefix = $method_functions['cache']['prefix'];
						}
						elseif($PROJECT['project'] && isset(PROJECTS[$PROJECT['project']]) && isset(PROJECTS[$PROJECT['project']]['cache']) && isset(PROJECTS[$PROJECT['project']]['cache']['prefix'])) {
							$cache_prefix = PROJECTS[$PROJECT['project']]['cache']['prefix'];
						}
						$actions[$_collection][$_action][$_method]['cache'] = array(
							'set' => function($content, $query) use($functions, $cache_time, $cache_prefix) {
								return $functions['cacheSet']($content, $cache_time, $query, $cache_prefix);
							}
						);
						if(
							(!isset($QUERY['_c']) || $QUERY['_c']) &&
							($_method == 'read')
						) {
							$actions[$_collection][$_action][$_method]['cache']['get'] = function($query) use($functions, $cache_prefix) {
								return $functions['cacheGet']($query, $cache_prefix);
							};
						}
					}
				}
				else {
					$actions[$_collection][$_action][$_method]['cache'] = false;
				}
				if(isset($method_functions['check']) && !$method_functions['check']) {
					unset($actions[$_collection][$_action][$_method]['check']);
				}
				elseif(!isset($method_functions['check'])) {
					$actions[$_collection][$_action][$_method]['check'] = $functions['defaultCheck'];
				}
				if(isset($method_functions['get']) && !$method_functions['get']) {
					unset($actions[$_collection][$_action][$_method]['get']);
				}
				elseif(!isset($method_functions['get'])) {
					$actions[$_collection][$_action][$_method]['get'] = $functions['defaultGet'];
				}
				if(isset($method_functions['set']) && !$method_functions['set']) {
					unset($actions[$_collection][$_action][$_method]['set']);
				}
				elseif(!isset($method_functions['set'])) {
					$actions[$_collection][$_action][$_method]['set'] = $functions['defaultSet'];
				}
			}
		}
	}
?>