<?php
session_start();
require_once 'includes/db.php';

// Получение данных из формы
$username = trim($_POST['username']);
$password = $_POST['password'];
$remember = isset($_POST['remember']);

// Поиск пользователя в базе
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Проверка пароля
if ($user && password_verify($password, $user['password_hash'])) {
    // Успешная авторизация
    $_SESSION['user_id'] = $user['id'];
    
    // Механизм "Запомнить меня"
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expire = time() + 60*60*24*30; // 30 дней
        
        setcookie('remember_token', $token, $expire, '/', '', true, true);
        
        $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $stmt->execute([hash('sha256', $token), $user['id']]);
    }
    
    header('Location: profile.php');
} else {
    $_SESSION['error'] = 'Неверный логин или пароль';
    header('Location: login.php');
}
