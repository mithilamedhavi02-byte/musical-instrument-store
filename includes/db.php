<?php
/**
 * Database Connection Class
 */
class Database {
    private $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            die("Database Connection Failed: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8mb4");
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return ['error' => $this->conn->error];
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        
        if ($stmt->error) {
            return ['error' => $stmt->error];
        }
        
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        
        if (isset($stmt['error'])) {
            return $stmt;
        }
        
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        
        if (isset($stmt['error'])) {
            return $stmt;
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        return $row;
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->query($sql, $values);
        
        if (isset($stmt['error'])) {
            return ['error' => $stmt['error']];
        }
        
        $insert_id = $stmt->insert_id;
        $stmt->close();
        
        return $insert_id;
    }
    
    public function update($table, $data, $where, $where_params = []) {
        $set = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
            $values[] = $value;
        }
        
        $set_clause = implode(', ', $set);
        $values = array_merge($values, $where_params);
        
        $sql = "UPDATE $table SET $set_clause WHERE $where";
        $stmt = $this->query($sql, $values);
        
        if (isset($stmt['error'])) {
            return ['error' => $stmt['error']];
        }
        
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Create global database instance
$db = new Database();
?>