<?php
/**
 * MathPlay Solutions — Update Avatar API Endpoint
 * Studio Game Over
 */

declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
requireApiRole('aluno');

$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
$avatar = trim($body['avatar'] ?? $_POST['avatar'] ?? '');

$allowed = ['avatar1', 'avatar2', 'avatar3', 'avatar4', 'avatar5', 'avatar6', 'avatar7', 'avatar8'];
if (!in_array($avatar, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Avatar inválido']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $pdo = getDB();
    $stmt = $pdo->prepare('UPDATE users SET avatar = :avatar WHERE id = :id');
    $stmt->execute([':avatar' => $avatar, ':id' => $userId]);

    $_SESSION['avatar'] = $avatar;
    $_SESSION['user_avatar'] = $avatar;

    echo json_encode(['success' => true, 'avatar' => $avatar, 'message' => 'Avatar atualizado com sucesso!']);
} catch (PDOException $e) {
    error_log('[update-avatar.php] DB error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados']);
}
