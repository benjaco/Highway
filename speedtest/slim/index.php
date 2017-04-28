<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 27-04-2017
 * Time: 22:47
 */

include "vendor/autoload.php";

ob_start();
$time_start = microtime(true);


$app = new \Slim\App();


$app->get("/", function () {
    ?>
    index
    <form action="" method="post">
        <input type="text" name="test">
        <input type="submit">
    </form>
    <?php
});

$app->post("/", function () {
    var_dump($_POST);
});

$app->get("/test", function () {
    echo "test";
});


$app->get("/testvar/{var}", function ($request, $response, $args) {
    var_dump($args);
});

$app->group("/user", function () use ($app) {

    $app->get("/{name}", function ($request, $response, $args) {
        echo "Wellcome " . $args['name'];
    });

    $app->get("/{name}/chat", function ($request, $response, $args) {
        echo "Wellcome to chat " . $args['name'];
    });
    $app->get("/{name}/chat/{friend}", function ($request, $response, $args) {
        echo "Wellcome " . $args['name'] . " chats with ".$args['friend'];
    });
});

$app->group("/login", function () use ($app) {
    $app->get("", function ($request, $response, $args) {
        echo "You are in";
    });
    $app->post("/post", function ($request, $response, $args) {
        var_dump($_POST);
    });

    $app->group("/settings", function () use ($app) {
        $app->get("", function ($request, $response, $args) {
            echo "Settings";
        });
        $app->get("/password", function ($request, $response, $args) {
            echo "Change the password";
        });
    });
    $app->group("/catalog", function () use ($app) {
        $app->get("/{catalog_name}", function ($request, $response, $args) {
            echo "catalog ".$args['catalog_name'];
        });
        $app->get("/{catalog_name}/image/{imgnr}", function ($request, $response, $args) {
            echo "catalog ".$args['catalog_name']." image nr ".$args['imgnr'];
        });
    });
});


$app->run();


ob_end_clean();
echo(microtime(true) - $time_start);