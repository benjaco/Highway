<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 29-04-2017
 * Time: 17:10
 */

class UsingClass{
    public static function Route($id = "No parameter")
    {
        echo "Route using class (static function) ". $id;
    }
    public function AnotherRoute($id = "No parameter")
    {
        echo "Route using class  ". $id;
    }

}