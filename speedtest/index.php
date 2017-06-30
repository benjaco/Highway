<?php

$slim = [
    "slim/",
    "slim/test",
    "slim/testvar/tesewter",
    "slim/user/benjamin",
    "slim/user/benjamin/chat",
    "slim/user/benjamin/chat/anden",
    "slim/login",
    "slim/login/settings",
    "slim/login/settings/password",
    "slim/login/catalog/testalbum",
    "slim/login/catalog/testalbum/image/5",
];
$highway = [
    "highway/",
    "highway/test",
    "highway/testvar/tesewter",
    "highway/user/benjamin",
    "highway/user/benjamin/chat",
    "highway/user/benjamin/chat/anden",
    "highway/login",
    "highway/login/settings",
    "highway/login/settings/password",
    "highway/login/catalog/testalbum",
    "highway/login/catalog/testalbum/image/5",
];

$prefix = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF'])."/";
function test($urls, $numberOfTimes){
    global $prefix;
    $totaltime = 0;

    for ($i = 0; $i < $numberOfTimes; $i++) {
        foreach ($urls as $url) {
            $time = file_get_contents( $prefix . $url);
            $time = floatval($time);
            $totaltime += $time;
        }
    }

    return $totaltime;
}

$slimtime = test($slim, 10);
sleep(2);
$highwaytime = test($highway, 10);

echo "Highway: {$highwaytime}, Slim: {$slimtime} <br> ";


$x_diff = number_format($slimtime/$highwaytime, 2);
echo "Highway is {$x_diff}x times faster then slim";