<?php

namespace system\core\database;

use system\core\AbstractProvider;

class Provider extends AbstractProvider{

    public $name = 'db';

    public function init()
    {
        $this->container->get('config')->setFromFile('database');

        $database = new Database(
            $this->container->get('config')->get('database', 'adaptor'),
            $this->container->get('config')->get('database', 'host'),
            $this->container->get('config')->get('database', 'user'),
            $this->container->get('config')->get('database', 'pass'),
            $this->container->get('config')->get('database', 'name')
        );

        $this->container->set($this->name, $database);
    }
}