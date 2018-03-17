<?php

require __DIR__.'/vendor/autoload.php';

$config = new \App\services\Config(__DIR__);
$routes = new \App\services\Routes($config);
