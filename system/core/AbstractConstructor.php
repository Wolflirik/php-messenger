<?php

namespace system\core;

abstract class AbstractConstructor{

    protected $container;

    /**
     * AbstractConstructor constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
}