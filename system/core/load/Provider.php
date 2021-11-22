<?php

namespace system\core\load;

use system\core\AbstractProvider;

class Provider extends AbstractProvider{

    public $name = 'load';

    public function init()
    {
        $load = new Load($this->container);
        $this->container->set($this->name, $load);
    }
}
