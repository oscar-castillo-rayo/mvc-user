<?php
class Database
{
    private static $host = 'localhost';
    private static $username = 'root';
    private static $password = '';
    private static $dbname = 'app_db';
    private static $conn;

    public static function getConnection()
    {
        self::$conn = null;
        try {
            self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$username, self::$password);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            echo "Connection database error: " . $error->getMessage();
        }
        return self::$conn;
    }
}
