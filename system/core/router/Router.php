<?php

namespace system\core\router;

class Router{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var $host
     */
    private $host;

    /**
     * @var $dispatcher
     */
    private $dispatcher;

    /**
     * Router constructor.
     * @param string $host
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * @param $key
     * @param $pattern
     * @param $controller
     * @param string $method
     */
    public function add($key, $pattern, $controller, $method = 'GET')
    {
        $this->routes[$key] = [
            'pattern'       => $pattern,
            'controller'    => $controller,
            'method'        => $method
        ];
    }

    /**
     * @return array
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * @param $method
     * @param $uri
     * @return mixed
     */
    public function dispatch($method, $uri)
    {
        return $this->getDispatcher()->dispatch($method, $uri);
    }

    /**
     * @return UriDispatcher
     */
    private function getDispatcher(){
        if($this->dispatcher == null)
        {
            $this->dispatcher = new UriDispatcher();
            foreach($this->routes as $route)
            {
                $this->dispatcher->register($route['method'], $route['pattern'], $route['controller']);
            }
        }
        return $this->dispatcher;
    }
}