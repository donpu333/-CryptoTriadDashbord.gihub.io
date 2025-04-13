<?php
session_start();
require_once 'includes/functions.php';
redirect_if_logged_in();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Вход в систему</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>
        
        <form action="includes/auth.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            
            <div class="form-group">
                <input type="text" name="username" placeholder="Логин" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember"> Запомнить меня
                </label>
            </div>
            
            <button type="submit" class="submit-btn">Войти</button>
        </form>
        
        <div class="links">
            <a href="forgot_password.php">Забыли пароль?</a>
            <a href="register.php">Создать аккаунт</a>
        </div>
    </div>
</body>
</html>
