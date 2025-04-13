<?php
require 'db_connect.php';
requireGuest();

$error = '';
$username = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Обновляем время последнего входа
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
                ->execute([$user['id']]);
            
            header("Location: profile.php");
            exit();
        } else {
            $error = 'Неверные учетные данные';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h2 class="text-center">Вход</h2>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Логин</label>
                    <input type="text" id="username" name="username" value="<?= $username ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Войти</button>
            </form>
            
            <div class="mt-20 text-center">
                Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>
            </div>
        </div>
    </div>
</body>
</html>
