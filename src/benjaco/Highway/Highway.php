<?php

namespace benjaco\Highway;

/**
 * Routes must be placed in a order where the most general patten is at the bottom
 */
class Highway
{
    /**
     * Current version
     *
     * @var string
     */
    const VERSION = '1.2.1';

    /**
     * @var bool|string
     */
    public static $url = false;
    public static $route_found = false;

    /**
     * @param bool $reletive_path allow url matching inside a subfolder without hardcodeing the path to the subfolder
     */
    public static function set_up($reletive_path = true)
    {

        self::$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($reletive_path && self::$url !== "/") {
            $cut_of = strlen(dirname($_SERVER['PHP_SELF']));
            self::$url = substr(self::$url, $cut_of);
        }

        if (substr(self::$url, -1) == "/") {
            self::$url = substr(self::$url, 0, -1);
        }
        if (self::$url == "") {
            self::$url = "/";
        }

    }

    /**
     * Redirect function for the parser
     * @param $name string Name of the placeholder patten
     * @param $regexPatten string The regular expression fro the patten, no need for outer grouping
     */
    public static function addOption($name, $regexPatten)
    {
        Parser::addOption($name, $regexPatten);
    }

    /**
     * Redirect function for the parser
     * @return array Return all the available options
     */
    public static function getOptions()
    {
        return Parser::getOptions();
    }

    /**
     * Prefix path for routes inside group(s)
     * @var string
     */
    private static $prefix_path = "";

    /**
     * Returns the current prefix for the group
     * @return string
     */
    public static function get_prefix_path()
    {
        return self::$prefix_path;
    }

    /**
     * Add a get route, and executes it immediately
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function get($patten, $callback)
    {
        if (self::$route_found || $_SERVER['REQUEST_METHOD'] !== "GET") {
            return false;
        }
        return self::any($patten, $callback);
    }

    /**
     * Add a post route, and executes it immediately
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function post($patten, $callback)
    {
        if (self::$route_found || $_SERVER['REQUEST_METHOD'] !== "POST") {
            return false;
        }
        return self::any($patten, $callback);
    }

    /**
     * Add a put route, and executes it immediately
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function put($patten, $callback)
    {
        if (self::$route_found || $_SERVER['REQUEST_METHOD'] !== "PUT") {
            return false;
        }
        return self::any($patten, $callback);
    }

    /**
     * Add a patch route, and executes it immediately
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function patch($patten, $callback)
    {
        if (self::$route_found || $_SERVER['REQUEST_METHOD'] !== "PATCH") {
            return false;
        }
        return self::any($patten, $callback);
    }

    /**
     * Add a delete route, and executes it immediately
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function delete($patten, $callback)
    {
        if (self::$route_found || $_SERVER['REQUEST_METHOD'] !== "DELETE") {
            return false;
        }
        return self::any($patten, $callback);
    }

    /**
     * Add a options route, and executes it immediately
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function options($patten, $callback)
    {
        if (self::$route_found || $_SERVER['REQUEST_METHOD'] !== "OPTIONS") {
            return false;
        }
        return self::any($patten, $callback);
    }

    /**
     * Add a route specified methods, and executes it immediately
     * @param $methods array Array of all methods there will execute this route, all methods MUST be in UPPERCASE
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function map(array $methods, $patten, $callback)
    {
        if (self::$route_found || !in_array($_SERVER['REQUEST_METHOD'], $methods)) {
            return false;
        }
        return self::any($patten, $callback);
    }

    /**
     * Add a route to accept any kind of method, and executes it immediately
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if route is executed
     */
    public static function any($patten, $callback)
    {
        if (self::$url === false) {
            Highway::set_up();
        }

        if ($patten == "/" && self::$prefix_path != "") {
            $patten = self::$prefix_path;
        } else {
            $patten = self::$prefix_path . $patten;
        }

        $checkPatten = Parser::checkPatten($patten, self::$url);
        if ($checkPatten[0]) {
            self::$route_found = true;

            call_user_func_array($callback, $checkPatten[1]);

            return true;

        }
        return false;
    }


    /**
     * Group routes, this can improve performance
     * @param $patten string Patten for the url, use {} for anonymous parameters, use {name} for named parameters to be added in the $_GET global variable
     * @param $callback callable Function to be called if patten matches
     * @return bool True if group is been fired
     */
    public static function group($patten, $callback)
    {
        if(Parser::matchGroup(self::$prefix_path . $patten, self::$url)){
            $original_prefix = self::$prefix_path;
            self::$prefix_path .= $patten;

            $callback();

            self::$prefix_path = $original_prefix;

            return true;
        }
        return false;
    }

    /**
     * This method must be the last thing to be called in the file, or in a group
     * @param $callback callable Function to be called if patten matches
     * @param bool $set_404_header Set a 404 status code by default
     * @return bool Return true if the route has been executed, otherwise false
     */
    public static function not_found($callback, $set_404_header = true)
    {
        if (!self::$route_found) {
            self::$route_found = true;
            if ($set_404_header) {
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            }
            $callback();
            return true;
        }
        return false;
    }


}
