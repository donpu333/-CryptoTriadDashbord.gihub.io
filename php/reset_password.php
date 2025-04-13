<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_GET['token'] ?? '';
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([hash('sha256', $token)]);
    $user = $stmt->fetch();
    
    if ($user && validate_password($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$passwordHash, $user['id']]);
        
        $_SESSION['success'] = 'Пароль успешно изменен!';
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Неверная ссылка или пароль не соответствует требованиям';
        header("Location: reset_password.php?token=$token");
        exit;
    }
}

$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Сброс пароля</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Сброс пароля</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <input type="password" name="password" placeholder="Новый пароль" required>
            </div>
            
            <button type="submit" class="submit-btn">Сменить пароль</button>
        </form>
    </div>
</body>
</html>
