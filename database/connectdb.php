<!-- database\connectdb.php -->
<?php

class Connect {
    protected $connection;

    public function __construct() {
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'novio';

        $this->connection = new mysqli($host, $username, $password, $dbname);

        if ($this->connection->connect_error) {
            die('Ошибка подключения: ' . $this->connection->connect_error);
        }
    }

    public function close() {
        $this->connection->close();
    }
}
?>