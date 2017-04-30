<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 27-04-2017
 * Time: 17:10
 */

use Highway\Highway;

include "../Highway/Highway.php";

spl_autoload_register(function ($name) {
    include "Routes/".$name.".php";
});

// setup can be called if you dont want the default parameter, or Highway::$url can be set
//Highway::set_up();


Highway::get("/", function () {
    ?>
    index
    <form action="" method="post">
        <input type="text" name="test">
        <input type="submit">
    </form>
    <?php
});

Highway::post("/", function () {
    var_dump($_POST);
});

Highway::get("/test", function () {
    echo "test";
});


Highway::get("/testvar/{}", function ($var) {
    echo $var;
});

Highway::group("/user", function () {

    Highway::get("/{name}", function () {
        echo "Wellcome " . $_GET['name'];
    });

    Highway::get("/{name}/chat", function () {
        echo "Wellcome to chat " . $_GET['name'];
    });
    Highway::get("/{name}/chat/{friend}", function () {
        echo "Wellcome " . $_GET['name'] . " chats with ".$_GET['friend'];
    });

    Highway::not_found(function () {
        echo "404 - No user page under this url was found";
    });
});

Highway::group("/login", function () {
    Highway::get("/", function () {
        echo "You are in";
    });
    Highway::post("/post", function () {
        var_dump($_POST);
    });

    Highway::group("/settings", function () {
        Highway::get("/", function () {
            echo "Settings";
        });
        Highway::get("/password", function () {
            echo "Change the password";
        });
    });
    Highway::group("/catalog/{catalog_name}", function () {
        Highway::get("/", function () {
            echo "catalog ".$_GET['catalog_name'];
        });
        Highway::get("/image/{imgnr}", function () {
            echo "catalog ".$_GET['catalog_name']." image nr ".$_GET['imgnr'];
        });
    });
});

Highway::serve_folder("/functions_from_folder", __DIR__ . "/phpfunctions/");

Highway::group("/old", function () {
    Highway::serve_folder("/phpfiles", __DIR__ . "/phpfunctions/", ["GET", "POST"]);
});


function using_function($id = "No parameter"){
    echo "Route using function ". $id;
}
Highway::get("/using_function", "using_function");
Highway::get("/using_function/{}", "using_function");


// using static functions in a class
// it will autoload the class only if its needed, if you are using a autoloader
Highway::get("/using_class", "UsingClass::Route");
Highway::get("/using_class/{}", "UsingClass::Route");


$classForRoutes = new UsingClass();
Highway::get("/using_class_init", [$classForRoutes, "AnotherRoute"] );
Highway::get("/using_class_init/{}", [$classForRoutes, "AnotherRoute"] );



Highway::not_found(function () {
    echo 404;
});

