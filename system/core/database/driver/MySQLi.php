<?php

namespace system\core\database\driver;

final class MySQLi {

    private $connection;

    /**
     * MySQLi constructor.
     * @param $hostname
     * @param $username
     * @param $password
     * @param $database
     * @param string $port
     * @throws \Exception
     */
    public function __construct($hostname, $username, $password, $database, $port = '3306') {
        $this->connection = new \mysqli($hostname, $username, $password, $database, $port);
        if ($this->connection->connect_errno) {
            throw new \Exception('Error: ' . $this->connection->connect_error . '<br />Error No: ' . $this->connection->connect_errno);
        }
        $this->connection->set_charset("utf8");
        $this->connection->query("SET SQL_MODE = ''");
    }

    /**
     * @param $sql
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function query($sql) {
        $query = $this->connection->query($sql);
        if (!$this->connection->errno) {
            if ($query instanceof \mysqli_result) {
                $data = array();
                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }
                $result = new \stdClass();
                $result->num_rows = $query->num_rows;
                $result->row = isset($data[0]) ? $data[0] : array();
                $result->rows = $data;
                $query->close();
                return $result;
            } else {
                return true;
            }
        } else {
            throw new \Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $sql);
        }
    }

    /**
     * @param $value
     * @return string
     */
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }

    /**
     * @return int
     */
    public function countAffected() {
        return $this->connection->affected_rows;
    }

    /**
     * @return mixed
     */
    public function getLastId() {
        return $this->connection->insert_id;
    }

    /**
     * @return bool
     */
    public function connected() {
        return $this->connection->ping();
    }

    public function __destruct() {
        $this->connection->close();
    }
}