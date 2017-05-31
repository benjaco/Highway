# Highway - a fast, simple, lightweight http router for PHP

This php router is 45 times faster then slim3, but highway are not having advanced stuff like middleware, it's a pure php router

Its is flexible, ex. it's is possible to add code in the start of a group 

A part of the speed reason is, this router is not collecting all routes before its run, is running as soon the route is defined, and if the url is not matching a specific group, then the groups callback won't be randed 

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