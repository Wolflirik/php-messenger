<?php

namespace system\core\router;

use system\core\AbstractProvider;

class Provider extends AbstractProvider{

    public $name = 'router';

    public function init()
    {
        $router = new Router($this->container->get('request')->server['HTTP_HOST']);
        $this->container->set($this->name, $router);
    }
}