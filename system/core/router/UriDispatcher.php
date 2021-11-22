<?php

namespace system\core\router;

class UriDispatcher{

    /**
     * @var array
     */
    private $routes = [
        'GET' => [],
        'POST' => []
    ];

    /**
     * @var array
     */
    private $pattern = [
        'int' => '[0-9]+',
        'str' => '[a-xA-Z\.\_%]+',
        'any' => '[a-xA-Z0-9\.\_%]+'
    ];

    /**
     * @param $key
     * @param $pattern
     */
    public function addPattern($key, $pattern)
    {
        $this->pattern[$key] = $pattern;
    }

    /**
     * @param $method
     * @return array|mixed
     */
    private function routes($method)
    {
        return isset($this->routes[$method]) ? $this->routes[$method] : [];
    }

    /**
     * @param $method
     * @param $pattern
     * @param $controller
     */
    public function register($method, $pattern, $controller)
    {
        $converted = $this->convertPattern($pattern);
        $this->routes[strtoupper($method)][$converted] = $controller;
    }

    /**
     * @param $pattern
     * @return mixed
     */
    private function convertPattern($pattern)
    {
        if(strpos($pattern, '(') === false)
        {
            return $pattern;
        }

        return preg_replace_callback('#\((\w+):(\w+)\)#', [$this, 'replacePattern'], $pattern);
    }

    /**\
     * @param $matches
     * @return string
     */
    private function replacePattern($matches)
    {
        return '(?<' . $matches[1] . '>' . strtr($matches[2], $this->pattern) . ')';
    }

    /**
     * @param $params
     * @return mixed
     */
    private function processParam($params)
    {
        foreach($params as $key => $val){
            if(is_int($key))
            {
                unset($params[$key]);
            }
        }
        return $params;
    }

    /**
     * @param $method
     * @param $uri
     * @return DispatchedRoute
     */
    public function dispatch($method, $uri)
    {
        // delete last "/"
        if($uri != '/')
        {
            $uri = substr($uri, strlen($uri)-1) == "/" ? substr($uri, 0, strlen($uri)-1) : $uri;
        }

        $routes = $this->routes(strtoupper($method));

        if(array_key_exists($uri, $routes))
        {
            return new DispatchedRoute($routes[$uri]);
        }

        return $this->doDispatch($method, $uri);
    }

    /**
     * @param $method
     * @param $uri
     * @return DispatchedRoute
     */
    private function doDispatch($method, $uri){
        foreach($this->routes($method) as $route => $controller)
        {
            $pattern = "#^" . $route . "$#s";

            if(preg_match($pattern, $uri, $params))
            {
                return new DispatchedRoute($controller, $this->processParam($params));
            }
        }
    }
}