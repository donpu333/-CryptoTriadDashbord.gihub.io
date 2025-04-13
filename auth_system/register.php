<?php
require 'db_connect.php';
requireGuest();

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Валидация
    if (empty($username)) {
        $errors['username'] = 'Логин обязателен';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Логин должен быть не менее 3 символов';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Логин может содержать только буквы, цифры и подчеркивания';
    }

    if (empty($email)) {
        $errors['email'] = 'Email обязателен';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email';
    }

    if (empty($password)) {
        $errors['password'] = 'Пароль обязателен';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Пароль должен быть не менее 6 символов';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Пароли не совпадают';
    }

    // Проверка уникальности
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $errors['general'] = 'Пользователь с таким логином или email уже существует';
        }
    }

    // Регистрация
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        $_SESSION['success_message'] = 'Регистрация прошла успешно. Теперь вы можете войти.';
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h2 class="text-center">Регистрация</h2>
            
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= $errors['general'] ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Логин</label>
                    <input type="text" id="username" name="username" value="<?= $username ?>" required>
                    <?php if (!empty($errors['username'])): ?>
                        <small style="color: var(--danger-color);"><?= $errors['username'] ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= $email ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <small style="color: var(--danger-color);"><?= $errors['email'] ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <small style="color: var(--danger-color);"><?= $errors['password'] ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Подтвердите пароль</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <small style="color: var(--danger-color);"><?= $errors['confirm_password'] ?></small>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn">Зарегистрироваться</button>
            </form>
            
            <div class="mt-20 text-center">
                Уже есть аккаунт? <a href="login.php">Войдите</a>
            </div>
        </div>
    </div>
</body>
</html>
