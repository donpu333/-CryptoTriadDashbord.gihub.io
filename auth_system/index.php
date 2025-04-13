<?php
require 'db_connect.php';

if (isLoggedIn()) {
    header("Location: profile.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
