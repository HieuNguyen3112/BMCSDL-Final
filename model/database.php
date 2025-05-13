<?php
// Bai02/model/database.php
class Database {
    protected $conn;

    public function __construct(array $config) {
        // Tạo kết nối mysqli
        $this->conn = new mysqli(
          $config['host'],
          $config['username'],
          $config['password'],
          $config['dbname']
        );
        if ($this->conn->connect_error) {
            die('Connect Error (' . $this->conn->connect_errno . '): ' . $this->conn->connect_error);
        }
        $this->conn->set_charset($config['charset']);
    }

    public function getConnection(){
      return $this->conn;
  }

    public function set_query(string $sql) {
        $this->sql = $sql;
    }

    public function excute_query() {
        return $this->conn->query($this->sql);
    }

    public function close() {
        $this->conn->close();
    }
}
