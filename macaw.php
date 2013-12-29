<?php

class Macaw {

	public static $routes = array();

	public static $methods = array();

	public static $callbacks = array();

	public static $patterns = array(
	        ':any' => '[^/]+',
	        ':num' => '[0-9]+',
	        ':all' => '.*'
    	);

	/**
	 * Defines a route w/ callback and method
	 */
	public static function __callstatic($method, $params) {
		$base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
		$uri = $base . $params[0];
		$callback = $params[1];

		array_push(self::$routes, $uri);
		array_push(self::$methods, strtoupper($method));
		array_push(self::$callbacks, $callback);
	}

	/**
	 * Runs the callback for the given request
	 */
	public function dispatch() {
		$uri = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$method = $_SERVER['REQUEST_METHOD'];

		$searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        $found_route = false;

        // check if route is defined without regex
		if (in_array($uri, self::$routes)) {
			$route_pos = array_keys(self::$routes, $uri);
			foreach ($route_pos as $route) {
				if (self::$methods[$route] == $method) {
					$found_route = true;
					call_user_func(self::$callbacks[$route]);
				}
			}
		} else {
			// check if defined with regex
			$pos = 0;
			foreach (self::$routes as $route) {
				if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

				if (preg_match('#^' . $route . '$#', $uri, $matched)) {
					if (self::$methods[$pos] == $method) {
						$found_route = true;
						call_user_func_array(self::$callbacks[$pos], array($matched[1]));
					}
				}
				$pos++;
			}
		}

		// return a 404 if the page was not found
		if ($found_route == false) {
			echo '404 :: Page not found';
		}
	}
}
