<?php

namespace system\container;

class Container{

    private $container = [];

    /**
     * @return array
     */
    public function get($key)
    {
        return $this->has($key)?$this->container[$key]:null;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->container[$key]);
    }

}