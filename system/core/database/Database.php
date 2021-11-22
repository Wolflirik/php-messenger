<?php

namespace system\core\database;

class Database {

    private $adaptor;

    /**
     * Constructor
     * @param	string	$adaptor
     * @param	string	$hostname
     * @param	string	$username
     * @param	string	$password
     * @param	string	$database
     * @param	int		$port
     */
    public function __construct($adaptor, $hostname, $username, $password, $database, $port = NULL) {
        $class = 'system\core\database\driver\\' . $adaptor;
        if (class_exists($class)) {
            $this->adaptor = new $class($hostname, $username, $password, $database, $port);
        } else {
            throw new \Exception('Error: Could not load database adaptor ' . $adaptor . '!');
        }
    }

    /**
     * @param $sql
     * @return mixed
     */
    public function query($sql) {
        return $this->adaptor->query($sql);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function escape($value) {
        return $this->adaptor->escape($value);
    }

    /**
     * @return mixed
     */
    public function countAffected() {
        return $this->adaptor->countAffected();
    }

    /**
     * @return mixed
     */
    public function getLastId() {
        return $this->adaptor->getLastId();
    }

    /**
     * @return mixed
     */
    public function connected() {
        return $this->adaptor->connected();
    }
}