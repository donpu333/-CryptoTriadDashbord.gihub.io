<?php
session_start();
require_once 'includes/db.php';

// Уничтожение сессии
session_unset();
session_destroy();

// Удаление remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

header('Location: login.php');
exit;
?>
