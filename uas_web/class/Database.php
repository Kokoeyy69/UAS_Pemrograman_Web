<?php
/**
 * Class Database - Hybrid Connection (MySQLi & PDO)
 * Mendukung fitur keamanan PDO & kompatibilitas MySQLi.
 */
class Database 
{ 
    protected $host; 
    protected $user; 
    protected $password; 
    protected $db_name; 
    public $conn; // Resource MySQLi
    public $pdo;  // Resource PDO

    public function __construct() 
    { 
        $this->getConfig(); 

        /**
         * 1. KONEKSI MySQLi (Legacy Support)
         */
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->db_name); 
        if ($this->conn->connect_error) { 
            die("Koneksi MySQLi Gagal: " . $this->conn->connect_error); 
        }

        /**
         * 2. KONEKSI PDO (Modern & Secure)
         * Menggunakan utf8mb4 agar mendukung simbol dan emoji.
         */
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            die("Koneksi PDO Gagal: " . $e->getMessage());
        }
    } 

    /**
     * Mengambil konfigurasi database.
     */
    private function getConfig() 
    { 
        if (file_exists(__DIR__ . "/../config.php")) {
            include __DIR__ . "/../config.php";
            $this->host     = $config['host']; 
            $this->user     = $config['username']; 
            $this->password = $config['password']; 
            $this->db_name  = $config['db_name']; 
        } else {
            // Fallback sesuai database di phpMyAdmin Anda
            $this->host     = "localhost";
            $this->user     = "root";
            $this->password = "";
            $this->db_name  = "latihan_oop"; 
        }
    } 

    /**
     * --- [PDO: runQuery] ---
     * Paling aman, gunakan ini untuk SELECT, INSERT, UPDATE, DELETE.
     */
    public function runQuery($sql, $params = []) 
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Memberikan pesan error teknis jika terjadi kesalahan kolom
            die("<div style='color:red; font-family:sans-serif; padding:20px; border:1px solid red;'>
                    <strong>Database Error:</strong> " . $e->getMessage() . "<br>
                    <strong>SQL Query:</strong> <code>" . htmlspecialchars($sql) . "</code>
                 </div>");
        }
    }

    /**
     * --- [MySQLi: query] ---
     * Alias untuk kompatibilitas modul lama agar tidak terjadi 'Call to undefined method'.
     */
    public function query($sql) 
    { 
        return $this->conn->query($sql); 
    } 

    /**
     * Ambil satu baris data (MySQLi)
     */
    public function get($table, $where = null) 
    { 
        if ($where) { $where = " WHERE " . $where; } 
        $sql = "SELECT * FROM " . $table . $where; 
        $result = $this->conn->query($sql); 
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null; 
    } 

    /**
     * Insert data (MySQLi)
     */
    public function insert($table, $data) 
    { 
        $column = [];
        $value = [];
        foreach ($data as $key => $val) { 
            $column[] = $key; 
            $value[] = "'" . $this->conn->real_escape_string($val) . "'"; 
        } 
        $sql = "INSERT INTO $table (" . implode(",", $column) . ") VALUES (" . implode(",", $value) . ")"; 
        return $this->conn->query($sql); 
    } 

    /**
     * Update data (MySQLi)
     */
    public function update($table, $data, $where) 
    { 
        $update_value = []; 
        foreach ($data as $key => $val) { 
            $val_safe = $this->conn->real_escape_string($val);
            $update_value[] = "$key='$val_safe'"; 
        } 
        $sql = "UPDATE $table SET " . implode(",", $update_value) . " WHERE " . $where; 
        return $this->conn->query($sql); 
    } 

    /**
     * Destructor: Menutup koneksi saat objek dihancurkan
     */
    public function __destruct() {
        $this->conn->close();
        $this->pdo = null;
    }
} 
?>