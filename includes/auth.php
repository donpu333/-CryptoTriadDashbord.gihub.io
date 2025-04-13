<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF защита
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ошибка безопасности');
    }

    // Очистка данных
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // Поиск пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Успешная авторизация
        $_SESSION['user_id'] = $user['id'];
        
        // Механизм "Запомнить меня"
        if ($remember) {
            $token = bin2hex(random_bytes(64));
            $expire = time() + 60*60*24*30; // 30 дней
            
            setcookie('remember_token', $token, [
                'expires' => $expire,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([hash('sha256', $token), $user['id']]);
        }
        
        header('Location: profile.php');
    } else {
        $_SESSION['error'] = 'Неверные учетные данные';
        header('Location: login.php');
    }
    exit;
}
