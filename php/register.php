<?php
session_start();
require_once 'includes/db.php';

$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Валидация
if ($password !== $confirm_password) {
    $_SESSION['error'] = 'Пароли не совпадают';
    header('Location: login.php');
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['error'] = 'Пароль должен быть не менее 8 символов';
    header('Location: login.php');
    exit;
}

// Проверка существования пользователя
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->rowCount() > 0) {
    $_SESSION['error'] = 'Пользователь с таким именем уже существует';
    header('Location: login.php');
    exit;
}

// Создание пользователя
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
$stmt->execute([$username, $password_hash]);

$_SESSION['success'] = 'Регистрация успешна! Войдите в систему';
header('Location: login.php');
