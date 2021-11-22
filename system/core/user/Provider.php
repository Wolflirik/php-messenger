<?php

namespace system\core\user;

use system\core\AbstractProvider;

class Provider extends AbstractProvider{

    public $name = 'user';

    public function init()
    {
        $user = new User($this->container);
        $this->container->set($this->name, $user);
    }
}