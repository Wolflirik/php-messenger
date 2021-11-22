<?php

namespace system;

use system\core\router\DispatchedRoute;
use system\helper\Common;

class System{

    /**
     * @var $container
     */
    private $container;

    /**
     * @var $router
     */
    private $router;

    /**
     * System constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->router = $this->container->get('router');
    }

    public function start()
    {
        try
        {
            require_once __DIR__ . '/../' . LOCATION . '/routes.php';
            $routerDispatch = $this->router->dispatch(Common::getMethod(), Common::getPathUri());

            //404
            if($routerDispatch == null) {
                $routerDispatch = new DispatchedRoute('error\Error:index');
            }

            list($class, $action) = explode(':', $routerDispatch->getController(), 2);
            $controller = "\\" . LOCATION . "\\controller\\" . $class;
            if (class_exists($controller))
            {
                //is logged?
                if(!$this->container->get('user')->isLogged() && !preg_match('~login~', Common::getPathUri())  && !preg_match('~register~', Common::getPathUri())){
                    header('Status: 302');
                    header('Location: ' . $this->container->get('config')->get('main', 'domain') . '/login');
                    exit();
                }

                foreach($routerDispatch->getParams() as $key => $param) {
                    $this->container->get('request')->get[$key] = $param;
                }

                call_user_func ([new $controller($this->container), $action]);
            }
            else
            {
                exit(sprintf('Controller %s not found!', $controller));
            }
        }
        catch(\Exception $e)
        {
            exit($e->getMessage());
        }

    }
}