<?php
/**
 * api/dashboard-stats.php — Fetch current student match statistics.
 */

declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

function jsonError(int $code, string $message): never
{
    http_response_code($code);
    exit(json_encode(['success' => false, 'message' => $message]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError(405, 'Método não permitido. Use GET.');
}

if (!isset($_SESSION['user_id'])) {
    jsonError(401, 'Não autenticado. Faça login para continuar.');
}

require_once dirname(__DIR__) . '/config/database.php';

try {
    $pdo = getDB();
    $userId = (int) $_SESSION['user_id'];

    $stmtRole = $pdo->prepare('SELECT tipo_usuario FROM users WHERE id = :id LIMIT 1');
    $stmtRole->execute([':id' => $userId]);
    if ($stmtRole->fetchColumn() !== 'aluno') {
        jsonError(403, 'Apenas alunos podem acessar estas estatísticas.');
    }

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS total_partidas,
                COALESCE(SUM(acertos), 0) AS total_acertos,
                COALESCE(SUM(erros), 0) AS total_erros
           FROM matches
          WHERE user_id = :user_id'
    );
    $stmt->execute([':user_id' => $userId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalAcertos = (int) $stats['total_acertos'];
    $totalErros = (int) $stats['total_erros'];
    $totalRespostas = $totalAcertos + $totalErros;

    echo json_encode([
        'success' => true,
        'total_partidas' => (int) $stats['total_partidas'],
        'total_acertos' => $totalAcertos,
        'total_erros' => $totalErros,
        'taxa_acerto' => $totalRespostas > 0
            ? (int) round(($totalAcertos / $totalRespostas) * 100)
            : 0,
    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (PDOException $e) {
    error_log('[dashboard-stats.php] DB error: ' . $e->getMessage());
    jsonError(500, 'Não foi possível atualizar as estatísticas agora.');
}
