<?php
// config/database.php

define('DB_HOST', 'sql100.infinityfree.com');
define('DB_NAME', 'if0_40114148_db2');
define('DB_USER', 'if0_40114148'); // Замініть на ваші дані
define('DB_PASS', 'Q9iA3W6lSntR13');     // Замініть на ваш пароль

// Підключення до БД (PDO)
class DB {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // У реальному проєкті тут краще логувати помилку, а не виводити користувачу
            die("Помилка підключення до бази даних: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}