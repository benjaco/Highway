<?php

namespace Highway;

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
    const VERSION = '1.0';

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
            throw new \LogicException("Highway::set_up() must be called before routing");
        }

        if ($patten == "/" && self::$prefix_path != "") {
            $patten = self::$prefix_path;
        } else {
            $patten = self::$prefix_path . $patten;
        }

        preg_match_all('/{[a-zA-Z0-9-_]*}/', $patten, $parameter_names);
        $parameter_names = $parameter_names[0];

        $patten = str_replace("/", '\/', $patten);
        $patten = preg_replace('/{[a-zA-Z0-9-_]*}/', '([^\/]*)', $patten);
        $patten = "/^" . $patten . "$/";


        if (preg_match_all($patten, self::$url, $parameter_values)) {

            // remove the first item, the first item is a match of the overall patten (the full sting, same as self::$url)
            array_shift($parameter_values);
            // the output from preg_match_all is nested
            $parameter_values = array_map(function ($value) {
                return $value[0];
            }, $parameter_values);


            foreach ($parameter_names as $parameter_index => $parameter_name) {
                // $parameter_name has {} before and after its self
                $plain_parameter_name = trim($parameter_name, "{}");
                // a developer can parse in {} if he dont cate about the output, or use the parameter method, so check for empty
                if ($plain_parameter_name != "") {
                    $_GET[$plain_parameter_name] = $parameter_values[$parameter_index];
                }
            }

            self::$route_found = true;

            call_user_func_array($callback, $parameter_values);
            return true;
        }

        return false;

    }

    /**
     * Serves ONLY php and html files from a folder, the full path with extension must be provided
     * @param $url_path string Route to access the folder
     * @param $foldername string Path to the folder to serve
     * @param string|array $methods String or array of methods there will execute this route, all methods MUST be in UPPERCASE
     * @return bool True if route is executed
     */
    public static function serve_folder($url_path, $foldername, $methods = "ALL")
    {
        if (self::$route_found) {
            return false;
        }

        if (self::$url === false) {
            throw new \LogicException("Highway::set_up() must be called before routing");
        }

        if ($methods !== "ALL") {
            if (is_string($methods)) {
                if ($methods !== $_SERVER['REQUEST_METHOD']) {
                    return false;
                }
            } elseif (is_array($methods)) {
                if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) {
                    return false;
                }
            }
        }

        $foldername = rtrim($foldername, "/");

        $query_patten = self::$prefix_path . $url_path;


        if (substr(self::$url, 0, strlen($query_patten)) === $query_patten) {

            $sub_path = substr(self::$url, strlen($query_patten));

            if ($sub_path === false) {
                if (file_exists($foldername . "/index.php") && is_file($foldername . "/index.php")) {
                    require $foldername . "/index.php";

                    self::$route_found = true;
                    return true;
                } else if (file_exists($foldername . "/index.html") && is_file($foldername . "/index.html")) {
                    require $foldername . "/index.html";

                    self::$route_found = true;
                    return true;
                }

            } else if (preg_match('/^(\/[a-zA-Z0-9-_]+)*\/[a-zA-Z0-9-_]+\.(html|php)$/', $sub_path)) { // matches urls like /folder/subfolder/file.php /subfolder/file.html file.php
                $full_file_path = $foldername . $sub_path;

                if (file_exists($full_file_path) && is_file($full_file_path)) {
                    require $full_file_path;
                    self::$route_found = true;
                    return true;
                }
            } else if (preg_match('/^(\/[a-zA-Z0-9-_]+)*\/[a-zA-Z0-9-_]+(\/)?$/', $sub_path)) { // matches urls like /folder/subfolder/ /folder/subfolder /folder
                $full_file_path = $foldername . rtrim($sub_path, "/");

                if (file_exists($full_file_path . "/index.php") && is_file($full_file_path . "/index.php")) {
                    require $full_file_path . "/index.php";

                    self::$route_found = true;
                    return true;
                } else if (file_exists($full_file_path . "/index.html") && is_file($full_file_path . "/index.html")) {
                    require $full_file_path . "/index.html";

                    self::$route_found = true;
                    return true;
                }

            }

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
        $check_patten = str_replace("/", '\/', self::$prefix_path . $patten);
        $check_patten = preg_replace('/{[a-zA-Z0-9-_]*}/', '([^\/]*)', $check_patten);
        $check_patten = "/^" . $check_patten . "/";

        if (preg_match($check_patten, self::$url)) {
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
        if ($set_404_header) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        }
        if (!self::$route_found) {
            $callback();
            return true;
        }
        return false;
    }


}
