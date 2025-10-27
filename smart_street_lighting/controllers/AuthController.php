<?php
// controllers/AuthController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    public function login($username, $password) {
        // Очищуємо вхідні дані
        $username = trim($username);
        $password = trim($password);
        
        $userModel = new User();
        // Викликаємо findByUsername лише ОДИН раз
        $user = $userModel->findByUsername($username); 

        // Перевірка, чи користувач існує І чи відповідає пароль хешу
        if ($user && password_verify($password, $user['password_hash'])) {
            // Успішний вхід
            // session_start() викликається у index.php, тут не потрібен, але перевірка не завадить
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Перенаправлення на дашборд (повний шлях)
            header('Location: /smart_street_lighting/dashboard'); 
            exit();
        } else {
            return "Невірний логін або пароль.";
        }
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        // Перенаправлення на головну/вхід (повний шлях)
        header('Location: /smart_street_lighting/'); 
        exit();
    }
}