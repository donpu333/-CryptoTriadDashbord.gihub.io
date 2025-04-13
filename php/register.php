<?php
session_start();
require_once 'includes/functions.php';
redirect_if_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Валидация
    if (!validate_password($password)) {
        $_SESSION['error'] = 'Пароль должен быть не менее 8 символов';
        header('Location: register.php');
        exit;
    }
    
    // Проверка уникальности
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = 'Пользователь с таким именем или email уже существует';
        header('Location: register.php');
        exit;
    }
    
    // Создание пользователя
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $passwordHash]);
    
    $_SESSION['success'] = 'Регистрация успешна! Войдите в систему';
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Регистрация</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Логин" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
            
            <button type="submit" class="submit-btn">Зарегистрироваться</button>
        </form>
        
        <div class="links">
            <a href="login.php">Уже есть аккаунт? Войти</a>
        </div>
    </div>
</body>
</html>
