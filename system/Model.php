<?php

namespace system;

use system\core\AbstractConstructor;

class Model extends AbstractConstructor{

    /**
     * Model constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key){
        return $this->container->get($key);
    }

    /**
     * @param $key
     * @param $val
     */
    public function __set($key, $val){
        $this->container->set($key, $val);
    }
}