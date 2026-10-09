<?php
/**
 * MathPlay Solutions — Cadastro API Endpoint
 * Studio Game Over
 *
 * Accepts: POST
 * Body: nome, username, email, senha, tipo_usuario, serie (aluno), avatar (aluno)
 * Returns: JSON {success: bool, message: string}
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido.',
    ]);
    exit;
}

require_once dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nome     = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$username = strtolower(trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''));
$email    = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
$senha    = trim($_POST['senha'] ?? '');
$tipoUsuario = trim($_POST['tipo_usuario'] ?? '');
$serie    = trim($_POST['serie'] ?? '');
$avatar   = trim($_POST['avatar'] ?? 'avatar1');

// Validate presence
if ($nome === '' || $username === '' || $email === '' || $senha === '' || $tipoUsuario === '') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Preencha os campos obrigatórios e selecione o tipo de conta.',
    ]);
    exit;
}

if (!in_array($tipoUsuario, ['aluno', 'professor'], true)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Tipo de conta inválido.',
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Formato de e-mail inválido.',
    ]);
    exit;
}

// Validate password length
if (strlen($senha) < 8) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'A senha deve ter no mínimo 8 caracteres.',
    ]);
    exit;
}

// Validate username (alphanumeric + underscore)
if (!preg_match('/^[a-z0-9_]{3,20}$/', $username)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Nome de usuário deve ter entre 3 e 20 caracteres (letras, números e underline).',
    ]);
    exit;
}

if ($tipoUsuario === 'aluno') {
    $allowedSeries = ['6°', '7°', '8°', '9°'];
    if (!in_array($serie, $allowedSeries, true)) {
        $serieMap = ['6' => '6°', '7' => '7°', '8' => '8°', '9' => '9°', '6º' => '6°', '7º' => '7°', '8º' => '8°', '9º' => '9°'];
        if (isset($serieMap[$serie])) {
            $serie = $serieMap[$serie];
        } else {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Selecione uma série escolar válida.',
            ]);
            exit;
        }
    }
} else {
    $serie = null;
}

// Validate avatar
$allowedAvatars = ['avatar1', 'avatar2', 'avatar3', 'avatar4', 'avatar5', 'avatar6', 'avatar7', 'avatar8'];
if (!in_array($avatar, $allowedAvatars, true)) {
    $avatar = 'avatar1';
}

try {
    $pdo = getDB();

    // Check existing email
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Este e-mail já está cadastrado.',
        ]);
        exit;
    }

    // Check existing username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Este nome de usuário já está em uso.',
        ]);
        exit;
    }

    // Hash password
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    // Insert user
    $stmt = $pdo->prepare(
        'INSERT INTO users (nome, username, email, senha, tipo_usuario, serie, avatar, xp, nivel, pontuacao)
         VALUES (:nome, :username, :email, :senha, :tipo_usuario, :serie, :avatar, 0, 1, 0)'
    );
    $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':senha', $senhaHash, PDO::PARAM_STR);
    $stmt->bindValue(':tipo_usuario', $tipoUsuario, PDO::PARAM_STR);
    $stmt->bindValue(':serie', $serie, $serie === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
    $stmt->execute();

    $userId = (int) $pdo->lastInsertId();

    if ($tipoUsuario === 'aluno') {
        $stmtProgress = $pdo->prepare(
            'INSERT INTO progress (user_id, jogo, melhor_pontuacao, partidas, acertos, erros, progresso)
             VALUES (:uid, :jogo, 0, 0, 0, 0, 0)'
        );
        $stmtProgress->execute([':uid' => $userId, ':jogo' => 'matematica']);
        $stmtProgress->execute([':uid' => $userId, ':jogo' => 'portugues']);
    }

    // Authenticate session
    session_regenerate_id(true);
    $_SESSION['user_id']  = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['nome']     = $nome;
    $_SESSION['tipo_usuario'] = $tipoUsuario;
    $_SESSION['nivel']    = 1;
    $_SESSION['xp']       = 0;
    $_SESSION['avatar']   = $avatar;
    $_SESSION['user_nome'] = $nome;
    $_SESSION['user_nivel'] = 1;
    $_SESSION['user_xp'] = 0;
    $_SESSION['user_avatar'] = $avatar;

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Cadastro realizado com sucesso! Bem-vindo ao MathPlay Solutions.',
        'redirect' => $tipoUsuario === 'professor' ? 'professor_dashboard.php' : 'dashboard.php',
    ]);
    exit;

} catch (PDOException $e) {
    error_log('[MathPlay Cadastro DB Error] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno ao realizar cadastro. Tente novamente mais tarde.',
    ]);
    exit;
}
