# Highway - a fast, simple, lightweight http router for PHP

This php router is 45 times faster then slim3, but highway are not having advanced stuff like middleware, it's a pure php router

Its is flexible, ex. it's is possible to add code in the start of a group 

A part of the speed reason is, this router is not collecting all routes before its run, is running as soon the route is defined, and if the url is not matching a specific group, then the groups callback won't be randed 

## Usage

```php
use Highway\Highway;

include "../Highway/Highway.php";


Highway::set_up();


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

Highway::serve_folder("/phpfiles", __DIR__ . "/phpfiles/");

Highway::not_found(function () {
    echo 404;
});
```

## Collaboration

All issues and pull requests are welcome