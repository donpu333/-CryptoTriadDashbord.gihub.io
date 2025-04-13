<?php
session_start();
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->execute([hash('sha256', $token), $expires, $user['id']]);
        
        // Отправка email (заглушка)
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        mail($email, "Восстановление пароля", "Для сброса пароля перейдите по ссылке: $resetLink");
    }
    
    $_SESSION['message'] = 'Если email зарегистрирован, вы получите инструкции';
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Восстановление пароля</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Восстановление пароля</h2>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Ваш email" required>
            </div>
            
            <button type="submit" class="submit-btn">Отправить ссылку</button>
        </form>
        
        <div class="links">
            <a href="login.php">Вспомнили пароль? Войти</a>
        </div>
    </div>
</body>
</html>
