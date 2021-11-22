<?php

namespace system\core\database\driver;

final class PDO
{
    private $connection;
    private $statement;

    /**
     * PDO constructor.
     * @param $hostname
     * @param $username
     * @param $password
     * @param $database
     * @param string $port
     * @throws \Exception
     */
    public function __construct($hostname, $username, $password, $database, $port = '3306') {
        try {
            $this->connection = @new \PDO("mysql:host=" . $hostname . ";port=" . $port . ";dbname=" . $database, $username, $password, array(\PDO::ATTR_PERSISTENT => true));
        } catch (\PDOException $e) {
            throw new \Exception('Error: Could not make a database link using ' . $username . '@' . $hostname . '!');
        }

        $this->connection->exec("SET NAMES 'utf8'");
        $this->connection->exec("SET CHARACTER SET utf8");
        $this->connection->exec("SET CHARACTER_SET_CONNECTION=utf8");
        $this->connection->exec("SET SQL_MODE = ''");
    }

    /**
     * @throws \Exception
     */
    public function execute() {
        try {
            if ($this->statement && $this->statement->execute()) {
                $data = [];

                while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                $result = new \stdClass();
                $result->row = (isset($data[0])) ? $data[0] : [];
                $result->rows = $data;
                $result->num_rows = $this->statement->rowCount();
            }
        } catch (\PDOException $e) {
            throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode());
        }
    }

    /**
     * @param $sql
     * @param array $params
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function query($sql, $params = []) {
        $this->statement = $this->connection->prepare($sql);

        $result = false;

        try {
            if ($this->statement && $this->statement->execute($params)) {
                $data = [];

                while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                $result = new \stdClass();
                $result->row = (isset($data[0]) ? $data[0] : []);
                $result->rows = $data;
                $result->num_rows = $this->statement->rowCount();
            }
        } catch (\PDOException $e) {
            throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode() . ' <br />' . $sql);
        }

        if ($result) {
            return $result;
        } else {
            $result = new \stdClass();
            $result->row = [];
            $result->rows = [];
            $result->num_rows = 0;

            return $result;
        }
    }

    /**
     * @param $sql
     */
    public function prepare($sql) {
        $this->statement = $this->connection->prepare($sql);
    }

    /**
     * @param $parameter
     * @param $variable
     * @param int $data_type
     * @param int $length
     */
    public function bindParam($parameter, $variable, $data_type = \PDO::PARAM_STR, $length = 0) {
        if ($length) {
            $this->statement->bindParam($parameter, $variable, $data_type, $length);
        } else {
            $this->statement->bindParam($parameter, $variable, $data_type);
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public function escape($value) {
        return str_replace(["\\", "\0", "\n", "\r", "\x1a", "'", '"'], ["\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'], $value);
    }

    /**
     * @return int
     */
    public function countAffected() {
        if ($this->statement) {
            return $this->statement->rowCount();
        } else {
            return 0;
        }
    }

    /**
     * @return string
     */
    public function getLastId() {
        return $this->connection->lastInsertId();
    }

    /**
     * @return bool
     */
    public function isConnected() {
        if ($this->connection) {
            return true;
        } else {
            return false;
        }
    }

    public function __destruct() {
        $this->connection = null;
    }
}