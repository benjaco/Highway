# Highway - a fast, simple, lightweight http router for PHP

This php router is 25 times faster then slim3, but highway are not having advanced stuff like middleware, it's a pure php router

Its is flexible, ex. it's is possible to add code in the start of a group 

A part of the speed reason is, this router is not collecting all routes before its run, is running as soon the route is defined, and if the url is not matching a specific group, then the groups callback won't be executed 

## Usage

```php
use Highway\Highway;

include "../Highway/Highway.php";

// setup can be called if you dont want the default parameter, or Highway::$url can be set
// Highway::set_up(false);


Highway::get("/", function () {
    echo "index";
});

Highway::get("/testvar/{}", function ($var) {
    echo $var;
});

Highway::group("/user", function () {

    Highway::get("/{name}", function () {
        echo "Wellcome " . $_GET['name'];
    });
    
});


// its posible to define a regex the parameter must match, but the regex cant contain capture groups
Highway::addOption("danish_phone_number", "(?:\+45)?\d{8}");
Highway::get("/number/{phone:danish_phone_number}", function (){
    echo "Phone nr: " . $_GET['phone'];
});
// int is a standard one there can be used as well
Highway::get("/testint/{:int}", function ($var) {
    echo $var;
});


// optional parameter with default value
function using_function($id = "No parameter"){
    echo "Route using function ". $id;
}
Highway::get("/using_function", "using_function");
Highway::get("/using_function/{}", "using_function");


// using static functions in a class
// it will autoload the class only if its needed, if you are using a autoloader
Highway::get("/using_class", "UsingClass::Route");
Highway::get("/using_class/{}", "UsingClass::Route");


// using methods from a instance of a class 
$classForRoutes = new UsingClass();
Highway::get("/using_class_init", [$classForRoutes, "AnotherRoute"] );
Highway::get("/using_class_init/{}", [$classForRoutes, "AnotherRoute"] );


Highway::not_found(function () {
    echo 404;
});
```

## Collaboration

All issues and pull requests are welcome