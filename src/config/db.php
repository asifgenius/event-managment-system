<?php
class Database {
    private static $connection = null;
    private $host = 'localhost';
    private $dbname = 'event_management_system';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            self::$connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    public static function getConnection() {
        if (self::$connection === null) {
            new self();
        }
        return self::$connection;
    }
}
?>
