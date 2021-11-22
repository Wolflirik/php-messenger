<?php

namespace system\core\request;

use system\core\AbstractProvider;

class Provider extends AbstractProvider{

    public $name = 'request';

    public function init()
    {
        $request = new Request();
        $this->container->set($this->name, $request);
    }
}