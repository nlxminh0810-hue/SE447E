<?php 

// Tạo class kết nối database
class Database {

    private $hostname = 'localhost';
    private $username = 'root';
    private $pass = '';
    private $dbname = 'englishai';

    protected $conn = NULL;
    private $result = NULL;

    public function connect() {
        $this->conn = new mysqli($this->hostname, $this->username, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            echo "Lỗi kết nối: " . $this->conn->connect_error;
        } else {
            mysqli_set_charset($this->conn, 'utf8');
        }
        return $this->conn;
    }

    // Hàm thực thi câu lệnh SQL
    public function execute($sql) {
        $this->result = $this->conn->query($sql);
        if (!$this->result) {
            die("SQL ERROR: " . $this->conn->error . "<br>QUERY: " . $sql);
        }
        return $this->result;
    }

    // Lấy 1 dòng dữ liệu
    public function getData() {
        if ($this->result) {
            $data = mysqli_fetch_assoc($this->result);
            if ($data === null) {
                $data = [];
            }
        } else {
            $data = [];
        }
        return $data;
    }

    // Lấy tất cả dữ liệu từ bảng
    public function getAllData($table) {
        $sql = "SELECT * FROM $table";
        $this->execute($sql);
        $data = [];
        while ($row = $this->getData()) {
            $data[] = $row;
        }
        return $data;
    }

    // Lấy dữ liệu với điều kiện WHERE
    public function getDataWhere($table, $where) {
        $sql = "SELECT * FROM $table WHERE $where";
        $this->execute($sql);
        return $this->getData();
    }

    // Insert dữ liệu
    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        return $this->execute($sql);
    }

    // Update dữ liệu
    public function update($table, $data, $where) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = '$value'";
        }
        $setClause = implode(", ", $set);
        $sql = "UPDATE $table SET $setClause WHERE $where";
        return $this->execute($sql);
    }

    // Delete dữ liệu
    public function delete($table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->execute($sql);
    }

    // Lấy số lượng bản ghi
    public function countData($table, $where = "") {
        $sql = "SELECT COUNT(*) as count FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        $this->execute($sql);
        $result = $this->getData();
        return $result['count'];
    }

    // Escape string for safe use in queries
    public function escapeString($str) {
        if ($this->conn === NULL) $this->connect();
        return $this->conn->real_escape_string($str);
    }

    public function getInsertId() {
        if ($this->conn === NULL) $this->connect();
        return (int)$this->conn->insert_id;
    }
}

?>
