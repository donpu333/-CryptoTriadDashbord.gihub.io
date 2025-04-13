<?php
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect_if_logged_in() {
    if (is_logged_in()) {
        header('Location: profile.php');
        exit;
    }
}

function validate_password($password) {
    return strlen($password) >= 8;
}
?>
