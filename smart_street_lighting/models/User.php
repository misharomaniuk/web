<?php
// models/User.php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance()->getConnection();
    }

    /**
     * Знаходить користувача за логіном.
     * @param string $username
     * @return array|false
     */
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Реєструє нового користувача.
     * @param string $username
     * @param string $password
     * @param string $role
     * @return bool
     */
    public function create($username, $password, $role = 'guest') {
        // Хешування пароля для безпеки
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':role', $role);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            // Обробка, наприклад, якщо логін вже існує
            return false;
        }
    }
}