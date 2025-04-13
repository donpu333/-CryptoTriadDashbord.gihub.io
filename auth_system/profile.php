<?php
require 'db_connect.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <h1>Ваш профиль</h1>
            <a href="logout.php" class="btn btn-danger">Выйти</a>
        </div>
        
        <div class="profile-info">
            <p><strong>Логин:</strong> <?= sanitize($user['username']) ?></p>
            <p><strong>Email:</strong> <?= sanitize($user['email']) ?></p>
            <p><strong>Дата регистрации:</strong> <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
            <?php if ($user['last_login']): ?>
                <p><strong>Последний вход:</strong> <?= date('d.m.Y H:i', strtotime($user['last_login'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
