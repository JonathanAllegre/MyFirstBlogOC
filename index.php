<?php

require __DIR__.'/vendor/autoload.php';


$config = new \App\services\Config();
$routes = new \App\services\Routes($config);
