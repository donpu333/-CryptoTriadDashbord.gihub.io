<?php
session_start();
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Проверка remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    require_once 'includes/db.php';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([hash('sha256', $_COOKIE['remember_token'])]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
    } else {
        header('Location: logout.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Профиль</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Добро пожаловать, <?= htmlspecialchars($_SESSION['username']) ?></h2>
        <a href="logout.php">Выйти</a>
    </div>
</body>
</html>
