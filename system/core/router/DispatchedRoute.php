<?php

namespace system\core\router;

class DispatchedRoute
{
    /**
     * @var $controller
     */
    private $controller;

    /**
     * @var $params
     */
    private $params;

    /**
     * DispatchedRoute constructor.
     * @param $controller
     * @param array $params
     */
    public function __construct($controller, $params = [])
    {
        $this->controller = $controller;
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getController(){
        return $this->controller;
    }

    /**
     * @return array
     */
    public function getParams(){
        return $this->params;
    }
}