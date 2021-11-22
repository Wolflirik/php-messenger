<?php

namespace system\core\load;

class Load
{

    private $container;

    /**
     * Load constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->container->get($key);
    }

    /**
     * @param $key
     * @param $val
     */
    public function __set($key, $val)
    {
        $this->container->set($key, $val);
    }

    /**
     * @param $model
     */
    public function model($model)
    {
        list($folder, $class) = explode('/', $model);

        $class_key = $class;

        if(mb_strpos($class, '_') !== false) {
            $class = explode('_', $class);
            $new_class = '';
            foreach ($class as $el) {
                $new_class .= ucfirst($el);
            }

            $class = $new_class;
        }else{
            $class = ucfirst($class);
        }

        $model_namespace = '\\' . LOCATION . '\\model\\' . $folder . '\\' . $class;
        if(class_exists($model_namespace))
        {
            $this->container->set('model_' . $folder . '_' . $class_key, new $model_namespace($this->container));
        }else{
            trigger_error('Error: Could not load model ' . $model_namespace . '!');
            exit();
        }
    }
}