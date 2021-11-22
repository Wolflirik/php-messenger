<?php

namespace system\core;

abstract class AbstractProvider{

    protected $container;

    /**
     * AbstractProvider constructor.
     * @param \system\container\Container $container
     */
    public function __construct(\system\container\Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    abstract function init();
}