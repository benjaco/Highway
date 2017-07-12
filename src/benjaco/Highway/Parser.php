<?php


namespace benjaco\Highway;

class Parser
{
    private static $options = [
        "int" => "\d+",
        "other_then_slash" => "[^\/]+"
    ];

    /**
     * @param $name string Name of the placeholder patten
     * @param $regexPatten string The regular expression for the patten, no need for outer grouping. The regex can't contain capture groups, only none capture groups are allowed
     * @throws ParserException
     */
    public static function addOption($name, $regexPatten)
    {
        if (self::numOfGroups($regexPatten) > 0) {
            throw new ParserException("The regex can't contain capture groups, only none capture groups are allowed, use (?:<patten>) instead of (<patten>) in ".$regexPatten);
        }
        self::$options[$name] = $regexPatten;
    }

    /**
     * @return array Return all the available options
     */
    public static function getOptions()
    {
        return self::$options;
    }

    /**
     * Finds the number of groups in a regular expression
     * @param $regexPatten string The regular expression
     * @return int Returns the number of groups inside the regular expression
     */
    public static function numOfGroups($regexPatten)
    {
        $regexPatten = str_replace('\\\\', "", $regexPatten); // remove all escaped backslashes
        $regexPatten = str_replace('\\(', "", $regexPatten); // remove all escaped open parentheses
        $regexPatten = str_replace('(?:', "", $regexPatten); // remove all none capture groups

        return substr_count($regexPatten, "("); // count the remaining opening parentheses
    }

    /**
     * Parses a route
     * @param $route string The route
     * @param $matchFirstPart bool Should it match a group or a route
     * @return array First item is the regular expression, second is the list of parameter names, null items in this list for inner groupings
     * @throws ParserException
     */
    public static function parse($route, $matchFirstPart)
    {
        $reqex = "";
        $parameterNames = [];

        $normalBuffer = "";
        $parameterBuffer = "";
        $insideParameter = false;


        foreach (str_split($route) as $char) {
            if ($insideParameter) {
                if ($char === "}") {

                    $parameterParts = explode(":", $parameterBuffer);
                    if (count($parameterParts) === 1) {
                        $parameterParts[] = "other_then_slash";
                    }


                    if (isset(self::$options[$parameterParts[1]])) {

                        $parameterNames[] = $parameterParts[0];


                        $reqex .= "(" . self::$options[$parameterParts[1]] . ")";

                    } else {
                        throw new ParserException("'" . $parameterParts[1] . "' was not found as a valid url parameter constrain in '" . $route . "'");
                    }

                    $parameterBuffer = "";

                    $insideParameter = false;

                } else {
                    $parameterBuffer .= $char;
                }
            } else {
                if ($char === "{") {
                    $reqex .= preg_quote($normalBuffer, "/");
                    $normalBuffer = "";

                    $insideParameter = true;
                } else {
                    $normalBuffer .= $char;
                }
            }
        }
        $reqex .= preg_quote($normalBuffer, "/");
        $reqex = "/^" . $reqex . (!$matchFirstPart ? "$" : "") . "/";

        return [$reqex, $parameterNames];
    }

    /**
     * @param $route string The route
     * @param $url string The url
     * @return array 1st item is true if its matching, if it does, second param will be an array of parameters for the callback
     */
    public static function checkPatten($route, $url)
    {
        list($patten, $parameter_names) = self::parse($route, false);

        if (preg_match_all($patten, $url, $parameter_values)) {

            // remove the first item, the first item is a match of the overall patten (the full sting, same as self::$url)
            array_shift($parameter_values);

            // the output from preg_match_all is nested
            $parameter_values = array_map(function ($value) {
                return $value[0];
            }, $parameter_values);

            if (count($parameter_values) !== count($parameter_names)) {
                throw new ParserException("The regex can't contain capture groups, only none capture groups are allowed, use (?:<patten>) instead of (<patten>) in folloowing route ".$route);
            }


            $callbackArgs = [];

            foreach ($parameter_names as $parameter_index => $parameter_name) {
                if (is_null($parameter_name)) {
                    continue;
                }
                if (!empty($parameter_name)) {
                    $_GET[$parameter_name] = $parameter_values[$parameter_index];
                }
                $callbackArgs[] = $parameter_values[$parameter_index];
            }


            return [true, $callbackArgs];
        }
        return [false];
    }

    public static function matchGroup($groupRoute, $url)
    {
        list($patten, $parameter_names) = self::parse($groupRoute, true);
        return preg_match_all($patten, $url);
    }
}
