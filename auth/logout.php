<?php

// session_start() wajib dipanggil dulu; tanpa ini session_destroy() tidak menghapus session apa pun
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

header("Location: ../index.php");
exit;