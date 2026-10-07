<?php
/**
 * MathPlay Solutions — Login API Endpoint
 * Studio Game Over
 *
 * Accepts: POST
 * Returns: JSON {success: bool, message: string}
 */

declare(strict_types=1);

// ── Headers ──────────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=UTF-8');

// ── Only accept POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido.',
    ]);
    exit;
}

// ── Dependencies ─────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/database.php';

// ── Start or resume session ───────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Sanitize & retrieve inputs ────────────────────────────────────────────────
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
$senha = trim($_POST['senha'] ?? '');

// ── Validate presence ────────────────────────────────────────────────────────
if ($email === '' || $senha === '') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'E-mail e senha são obrigatórios.',
    ]);
    exit;
}

// ── Validate email format ────────────────────────────────────────────────────
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Formato de e-mail inválido.',
    ]);
    exit;
}

// ── Database lookup ───────────────────────────────────────────────────────────
try {
    $pdo = getDB();

    $stmt = $pdo->prepare(
        'SELECT id, username, nome, senha, nivel, xp, avatar
         FROM users
         WHERE email = :email
         LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('[MathPlay Login DB Error] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno. Tente novamente mais tarde.',
    ]);
    exit;
}

// ── Verify credentials ────────────────────────────────────────────────────────
if (!$user || !password_verify($senha, $user['senha'])) {
    // Uniform message to avoid user enumeration
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'E-mail ou senha incorretos.',
    ]);
    exit;
}

// ── Establish authenticated session ──────────────────────────────────────────
session_regenerate_id(true);

$_SESSION['user_id']  = (int) $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['nome']     = $user['nome'];
$_SESSION['nivel']    = (int) ($user['nivel'] ?? 1);
$_SESSION['xp']       = (int) ($user['xp']    ?? 0);
$_SESSION['avatar']   = $user['avatar'] ?? 'avatar1';

// ── Success response ──────────────────────────────────────────────────────────
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Login realizado com sucesso!',
]);
exit;
