<?php

namespace system\core\config;

class Config
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @param $group
     * @param $name
     * @return mixed|bool
     */
    public function get($group, $name){
        return isset($this->config[$group][$name]) ? $this->config[$group][$name] : null;
    }

    /**
     * @param $name
     * @throws \Exception
     */
    public function setFromFile($name) {
        $path = __DIR__ . '/../../config/' . $name . '.php';

        if(file_exists($path)) {
            $config = require($path);

            if (!empty($config)) {
                $this->config[$name] = $config;
            } else {
                throw new \Exception(sprintf('File %s is empty!', $name));
            }
        } else {
            throw new \Exception(sprintf('File %s not found in %s!', $name, $path));
        }
    }

    /**
     * @param $name
     * @param $data
     * @throws \Exception
     */
    public function setFromData($name, $data) {
        if(!empty($data)) {
            $this->config[$name] = isset($this->config[$name]) ? array_merge($this->config[$name], $data) : $data;
        } else {
            throw new \Exception(sprintf('no data to write to the group %s!', $name));
        }
    }
}