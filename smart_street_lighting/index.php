<?php
// index.php (Головний маршрутизатор)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';
// ... інші контролери

// Проста логіка маршрутизації (clean URLs)
$request_uri = '';

// Якщо використовується Варіант А .htaccess, шлях може бути тут:
if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
    $request_uri = $_SERVER['PATH_INFO']; 
} elseif (isset($_SERVER['REDIRECT_URL']) && !empty($_SERVER['REDIRECT_URL'])) {
    // Деякі сервери використовують REDIRECT_URL
    $request_uri = $_SERVER['REDIRECT_URL']; 
    // Вам може знадобитися видалити префікс /smart_street_lighting/ з цього шляху
    $project_prefix = '/smart_street_lighting';
    if (str_starts_with($request_uri, $project_prefix)) {
        $request_uri = substr($request_uri, strlen($project_prefix));
    }
} else {
    // В якості fallback використовуємо REQUEST_URI
    $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $project_prefix = '/smart_street_lighting';
    if (str_starts_with($request_uri, $project_prefix)) {
        $request_uri = substr($request_uri, strlen($project_prefix));
    }
}


$segments = explode('/', trim($request_uri, '/'));

// Визначення маршруту
$route = $segments[0] ?: 'home';

// Перевірка авторизації (для більшості сторінок)
$is_authenticated = isset($_SESSION['user_id']);

switch ($route) {
    case 'login':
        // Цей блок тепер обробляє випадки, коли сервер правильно зрозумів 'clean URL' /login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new AuthController();
            $error = $auth->login($_POST['username'], $_POST['password']);
            require 'views/auth/login.php'; 
        } else {
            require 'views/auth/login.php';
        }
        break;
        
    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;
        
    case 'dashboard':
        if (!$is_authenticated) { header('Location: /login'); exit(); }
        // Перевірка ролі може бути тут
        $dashboard = new DashboardController();
        $data = $dashboard->index(); // Отримати дані для дашборду
        require 'views/dashboard.php';
        break;
        
    case 'lighting-control':
        if (!$is_authenticated || $_SESSION['role'] === 'guest') { header('Location: /login'); exit(); }
        // Обробка POST-запиту на ввімкнення/вимкнення
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            // Логіка керування
        }
        // Завантаження сторінки з керуванням
        // require 'views/control.php'; 
        break;

case 'home':
    case '': 
    default:
        // *** НОВИЙ БЛОК ДЛЯ ОБРОБКИ POST-ЗАПИТІВ НА КОРЕНІ ***
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
            // Якщо це POST-запит і є дані логіну, обробляємо його як спробу входу
            $auth = new AuthController();
            $error = $auth->login($_POST['username'], $_POST['password']);
            // Якщо вхід неуспішний, показуємо форму з помилкою
            require 'views/auth/login.php'; 
        } 
        // *** КІНЕЦЬ НОВОГО БЛОКУ ***
        
        else if ($is_authenticated) {
             header('Location: /smart_street_lighting/dashboard'); // Використовуйте повний шлях перенаправлення
        } else {
             // Якщо це GET-запит і не авторизований, просто показуємо форму логіну
             require 'views/auth/login.php'; 
        }
        break;
}