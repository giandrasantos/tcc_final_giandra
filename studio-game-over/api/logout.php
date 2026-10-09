<?php
/**
 * MathPlay Solutions — Logout Endpoint
 * Studio Game Over
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

if (isset($_GET['json'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => 'Sessão encerrada']);
    exit;
}

$logoutRedirect = $logoutRedirect ?? '../index.php';
header('Location: ' . $logoutRedirect);
exit;
