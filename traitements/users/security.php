<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: /traitements/users/login.php');
    exit();
}
