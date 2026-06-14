<?php
class ExamDatabase {
    private $host = "localhost";
    private $db_name = "sms101"; // this is your second database
    private $username = "root";
    private $password = "pwd@123";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Exam DB Error: " . $e->getMessage());
        }

        return $this->conn;
    }
    
}
?>
