<?php

namespace NoahBuscher\Macaw;

/**
 * @method static Macaw get(string $route, Callable $callback)
 * @method static Macaw post(string $route, Callable $callback)
 * @method static Macaw put(string $route, Callable $callback)
 * @method static Macaw delete(string $route, Callable $callback)
 * @method static Macaw options(string $route, Callable $callback)
 * @method static Macaw head(string $route, Callable $callback)
 */

class Macaw
{

    public static $halts = true;

    public static $routes = array();

    public static $methods = array();

    public static $callbacks = array();

    public static $patterns = array(
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    );

    public static $error_callback;

    /**
     * Defines a route w/ callback and method
     */
    public static function __callstatic($method, $params) 
    {
      
        $uri = dirname($_SERVER['PHP_SELF']).'/'.$params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    /**
     * Defines callback if route is not found
    */
    public static function error($callback)
    {
        self::$error_callback = $callback;
    }

    /**
     * Don't load any further routes on match
     * @param  boolean $flag 
     */
    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    /**
     * call object and instantiate
     * @param  object $callback 
     * @param  array $matched  array of matched parameters
     * @param  string $msg      
     */
    public static function invokeObject($callback,$matched = null,$msg = null)
    {
        //grab all parts based on a / separator 
        $parts = explode('/',$callback);

        //collect the last index of the array
        $last = end($parts);

        //grab the controller name and method call
        $segments = explode('@',$last);                         

        //instanitate controller with optional msg (used for error_callback)
        $controller = new $segments[0]($msg);

        if($matched == null){

            //call method
            $controller->$segments[1]();

        } else {

            //call method and pass any extra parameters to the method
            $controller->$segments[1](implode(",", $matched));
        }
    }

    /**
     * Runs the callback for the given request
     */
    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];  

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace('//','/',self::$routes);   

        $found_route = false;

        // check if route is defined without regex
        if (in_array($uri, self::$routes)) {
            $route_pos = array_keys(self::$routes, $uri);
            foreach ($route_pos as $route) {

                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    $found_route = true;

                    //if route is not an object 
                    if(!is_object(self::$callbacks[$route])){

                        //call object controller and method
                        self::invokeObject(self::$callbacks[$route]);

                        if (self::$halts) return;
                        
                    } else { 

                        //call closure
                        call_user_func(self::$callbacks[$route]);

                        if (self::$halts) return;
                    }
                }
            }
        } else {

            // check if defined with regex
            $pos = 0;
            foreach (self::$routes as $route) {

                $route = str_replace('//','/',$route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {

                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
                        $found_route = true; 

                        array_shift($matched); //remove $matched[0] as [1] is the first parameter.

                        if(!is_object(self::$callbacks[$pos])){

                            //call object controller and method
                            self::invokeObject(self::$callbacks[$pos],$matched);

                            if (self::$halts) return;
                            
                        } else {
                            //call closure
                            call_user_func_array(self::$callbacks[$pos], $matched);
                       
                            if (self::$halts) return;
                        }
                        
                    }
                }
            $pos++;
            }
        }
 

        // run the error callback if the route was not found
        if ($found_route == false) {
            if (!self::$error_callback) {
                self::$error_callback = function() {
                    header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
                    echo '404';
                };
            } 

            if(!is_object(self::$error_callback)){

                //call object controller and method
                self::invokeObject(self::$error_callback,null,'No routes found.');

                if (self::$halts) return;

            } else {
               
               call_user_func(self::$error_callback); 

               if (self::$halts) return;
            }
            
        }
    }
}
