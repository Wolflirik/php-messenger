<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use system\System;
use system\container\Container;

try{
    $container = new Container();
    $services = require_once 'config/service.php';

    foreach($services as $service)
    {
        $provider = new $service($container);
        $provider->init();
    }

    $system = new System($container);
    $system->start();
}
catch(\Exception $e)
{
    exit($e->getMessage());
}