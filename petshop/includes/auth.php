<?php
// includes/auth.php
session_start();
function is_logged_in() { return isset($_SESSION['user']); }
function require_login() {
    if(!is_logged_in()) { header('Location: /login.php?next=' . urlencode($_SERVER['REQUEST_URI'] ?? '/')); exit; }
}
?>