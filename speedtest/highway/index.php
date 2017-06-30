<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 27-04-2017
 * Time: 17:10
 */


use benjaco\Highway\Highway;

include "../../src/benjaco/Highway/Highway.php";
include "../../src/benjaco/Highway/Parser.php";
include "../../src/benjaco/Highway/ParserException.php";


ob_start();
$time_start = microtime(true);

Highway::set_up();


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

Highway::not_found(function () {
    echo 404;
});


ob_end_clean();
echo(microtime(true) - $time_start);