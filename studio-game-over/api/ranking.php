<?php
/**
 * api/ranking.php — Global and Per-Game Leaderboard
 * MathPlay Solutions | Studio Game Over
 *
 * GET  ?filtro=geral&limit=10
 *
 * filtro values
 * ─────────────
 *  geral      — Ordered by usuarios.pontuacao_total DESC
 *  matematica — Ordered by progress.melhor_pontuacao DESC (jogo = 'matematica')
 *  portugues  — Ordered by progress.melhor_pontuacao DESC (jogo = 'portugues')
 *
 * Response
 * ─────────
 * {
 *   success: true,
 *   filtro:  'geral',
 *   ranking: [
 *     { posicao, user_id, nome, username, nivel, xp, pontuacao, avatar }
 *   ],
 *   posicao_atual: {   // current user's position (may be > limit)
 *     posicao, user_id, nome, username, nivel, xp, pontuacao, avatar
 *   } | null
 * }
 */

declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode([
        'success' => false,
        'message' => 'Não autenticado. Faça login para continuar.',
    ]));
}

// ── Dependencies ──────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/database.php';

// ── Helper ────────────────────────────────────────────────────────────────────
function jsonError(int $code, string $message): never
{
    http_response_code($code);
    exit(json_encode(['success' => false, 'message' => $message]));
}

// ── Only GET allowed ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError(405, 'Método não permitido. Use GET.');
}

// ── Input validation ──────────────────────────────────────────────────────────
$allowedFiltros = ['geral', 'matematica', 'portugues'];

$filtro = trim(strtolower($_GET['filtro'] ?? 'geral'));
$limit  = (int) ($_GET['limit'] ?? 10);

if (!in_array($filtro, $allowedFiltros, true)) {
    jsonError(400, 'Parâmetro "filtro" inválido. Use: ' . implode(', ', $allowedFiltros));
}

// Clamp limit: between 1 and 100
$limit  = max(1, min(100, $limit));
$userId = (int) $_SESSION['user_id'];

// ── Build queries ─────────────────────────────────────────────────────────────
try {
    $pdo = getDB();

    if ($filtro === 'geral') {
        // Top list
        $stmtTop = $pdo->prepare(
            'SELECT u.id         AS user_id,
                    u.nome,
                    u.username,
                    u.nivel,
                    u.xp,
                    u.avatar,
                    u.pontuacao AS pontuacao
               FROM users AS u
              ORDER BY u.pontuacao DESC, u.xp DESC
              LIMIT :lim'
        );
        $stmtTop->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmtTop->execute();
        $topRows = $stmtTop->fetchAll();

        // Current user's position in the full table
        $stmtPos = $pdo->prepare(
            'SELECT COUNT(*) + 1 AS posicao
               FROM users
              WHERE pontuacao > (
                        SELECT pontuacao FROM users WHERE id = :uid
                    )'
        );
        $stmtPos->execute([':uid' => $userId]);
        $userPosition = (int) $stmtPos->fetchColumn();

        // Current user's own row
        $stmtSelf = $pdo->prepare(
            'SELECT id AS user_id,
                    nome,
                    username,
                    nivel,
                    xp,
                    avatar,
                    pontuacao AS pontuacao
               FROM users
              WHERE id = :uid
              LIMIT 1'
        );
        $stmtSelf->execute([':uid' => $userId]);
        $selfRow = $stmtSelf->fetch();

    } else {
        $jogo = $filtro; // 'matematica' | 'portugues'

        // Top list
        $stmtTop = $pdo->prepare(
            'SELECT u.id                  AS user_id,
                    u.nome,
                    u.username,
                    u.nivel,
                    u.xp,
                    u.avatar,
                    p.melhor_pontuacao    AS pontuacao
               FROM progress AS p
               JOIN users AS u ON u.id = p.user_id
              WHERE p.jogo = :jogo
              ORDER BY p.melhor_pontuacao DESC, u.xp DESC
              LIMIT :lim'
        );
        $stmtTop->bindValue(':jogo', $jogo,  PDO::PARAM_STR);
        $stmtTop->bindValue(':lim',  $limit, PDO::PARAM_INT);
        $stmtTop->execute();
        $topRows = $stmtTop->fetchAll();

        // Current user's position within the jogo ranking
        $stmtPos = $pdo->prepare(
            'SELECT COUNT(*) + 1 AS posicao
               FROM progress
              WHERE jogo = :jogo
                AND melhor_pontuacao > COALESCE(
                        (SELECT melhor_pontuacao FROM progress WHERE user_id = :uid AND jogo = :jogo2),
                        -1
                    )'
        );
        $stmtPos->execute([':jogo' => $jogo, ':uid' => $userId, ':jogo2' => $jogo]);
        $userPosition = (int) $stmtPos->fetchColumn();

        // Current user's own row for this jogo
        $stmtSelf = $pdo->prepare(
            'SELECT u.id               AS user_id,
                    u.nome,
                    u.username,
                    u.nivel,
                    u.xp,
                    u.avatar,
                    p.melhor_pontuacao AS pontuacao
               FROM users AS u
          LEFT JOIN progress AS p ON p.user_id = u.id AND p.jogo = :jogo
              WHERE u.id = :uid
              LIMIT 1'
        );
        $stmtSelf->execute([':jogo' => $jogo, ':uid' => $userId]);
        $selfRow = $stmtSelf->fetch();
    }

} catch (PDOException $e) {
    error_log('[ranking.php] DB error: ' . $e->getMessage());
    jsonError(500, 'Erro ao carregar ranking. Tente novamente mais tarde.');
}

// ── Add position numbers to top list ─────────────────────────────────────────
$ranking = [];
foreach ($topRows as $index => $row) {
    $ranking[] = [
        'posicao'   => $index + 1,
        'user_id'   => (int) $row['user_id'],
        'nome'      => $row['nome'],
        'username'  => $row['username'],
        'nivel'     => (int) $row['nivel'],
        'xp'        => (int) $row['xp'],
        'pontuacao' => (int) $row['pontuacao'],
        'avatar'    => $row['avatar'],
    ];
}

// ── Build current user's position object ─────────────────────────────────────
$posicaoAtual = null;
if ($selfRow) {
    $posicaoAtual = [
        'posicao'   => $userPosition,
        'user_id'   => (int) $selfRow['user_id'],
        'nome'      => $selfRow['nome'],
        'username'  => $selfRow['username'],
        'nivel'     => (int) $selfRow['nivel'],
        'xp'        => (int) $selfRow['xp'],
        'pontuacao' => (int) ($selfRow['pontuacao'] ?? 0),
        'avatar'    => $selfRow['avatar'],
    ];
}

// ── Respond ───────────────────────────────────────────────────────────────────
echo json_encode([
    'success'       => true,
    'filtro'        => $filtro,
    'ranking'       => $ranking,
    'posicao_atual' => $posicaoAtual,
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
