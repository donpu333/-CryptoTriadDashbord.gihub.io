<?php
session_start();

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'auth_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Подключение к базе данных
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для защиты от XSS
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Проверка авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Перенаправление если не авторизован
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Перенаправление если авторизован
function requireGuest() {
    if (isLoggedIn()) {
        header("Location: profile.php");
        exit();
    }
}
?>
