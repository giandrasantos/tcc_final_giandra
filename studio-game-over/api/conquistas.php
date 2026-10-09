<?php
/**
 * api/conquistas.php — Evaluate and Grant Achievements
 * MathPlay Solutions | Studio Game Over
 *
 * POST  Content-Type: application/json
 * Body: {
 *   jogo:              'matematica' | 'portugues',
 *   acertos:           int,
 *   erros:             int,
 *   pontuacao:         int,
 *   sequencia_maxima:  int,   (longest consecutive correct streak in this match)
 * }
 *
 * Achievement definitions
 * ────────────────────────
 *  primeira_partida        — First ever match completed.
 *  sequencia_5             — Correct streak of 5+ in a single match.
 *  pontos_matematica_500   — Cumulative best score in math >= 500.
 *  pontos_portugues_500    — Cumulative best score in Portuguese >= 500.
 *  partida_perfeita        — No errors and at least 5 correct in one match.
 *  nivel_mestre            — User has reached level 5.
 *
 * Returns: { success: true, novas_conquistas: [{id, nome, descricao, icone}] }
 */

declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

// ── Dependencies ──────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
requireApiRole('aluno');

// ── Helper ────────────────────────────────────────────────────────────────────
function jsonError(int $code, string $message): never
{
    http_response_code($code);
    exit(json_encode(['success' => false, 'message' => $message]));
}

// ── Only POST allowed ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError(405, 'Método não permitido. Use POST.');
}

// ── Parse JSON body ───────────────────────────────────────────────────────────
$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body)) {
    jsonError(400, 'JSON inválido no corpo da requisição.');
}

// ── Validate inputs ───────────────────────────────────────────────────────────
$allowedJogos = ['matematica', 'portugues'];

$jogo             = trim((string) ($body['jogo']             ?? ''));
$acertos          = (int)          ($body['acertos']          ?? 0);
$erros            = (int)          ($body['erros']            ?? 0);
$pontuacao        = (int)          ($body['pontuacao']        ?? 0);
$sequenciaMaxima  = (int)          ($body['sequencia_maxima'] ?? 0);

if (!in_array($jogo, $allowedJogos, true)) {
    jsonError(400, 'Campo "jogo" inválido. Use: ' . implode(', ', $allowedJogos));
}

if ($acertos < 0 || $erros < 0 || $pontuacao < 0 || $sequenciaMaxima < 0) {
    jsonError(400, 'Os campos numéricos devem ser inteiros não-negativos.');
}

// ── DB Setup ──────────────────────────────────────────────────────────────────
$userId = (int) $_SESSION['user_id'];

try {
    $pdo = getDB();

    // ── Pre-fetch data needed for multiple checks ─────────────────────────────

    // User's current nivel
    $stmtUser = $pdo->prepare('SELECT nivel FROM users WHERE id = :id LIMIT 1');
    $stmtUser->execute([':id' => $userId]);
    $userData = $stmtUser->fetch();

    if (!$userData) {
        jsonError(404, 'Usuário não encontrado.');
    }

    $userNivel = (int) $userData['nivel'];

    // Total matches ever played by this user (for primeira_partida check)
    $stmtCount = $pdo->prepare(
        'SELECT COUNT(*) AS total FROM matches WHERE user_id = :uid'
    );
    $stmtCount->execute([':uid' => $userId]);
    $totalPartidas = (int) $stmtCount->fetchColumn();

    // Cumulative best scores from progress table
    $stmtProgress = $pdo->prepare(
        'SELECT jogo, melhor_pontuacao FROM progress WHERE user_id = :uid'
    );
    $stmtProgress->execute([':uid' => $userId]);
    $progressRows = $stmtProgress->fetchAll();

    $melhorPontuacao = ['matematica' => 0, 'portugues' => 0];
    foreach ($progressRows as $row) {
        $melhorPontuacao[$row['jogo']] = (int) $row['melhor_pontuacao'];
    }

    // Fetch already-earned achievement IDs for this user
    $stmtOwned = $pdo->prepare(
        'SELECT achievement_id FROM user_achievements WHERE user_id = :uid'
    );
    $stmtOwned->execute([':uid' => $userId]);
    $ownedIds = $stmtOwned->fetchAll(PDO::FETCH_COLUMN, 0);
    $ownedSet = array_flip($ownedIds); // O(1) lookup

    // Map requirement strings to achievement IDs (1 to 6)
    // 1: primeira_partida, 2: sequencia_5, 3: pontos_matematica_500, 4: pontos_portugues_500, 5: partida_perfeita, 6: nivel_mestre
    $candidates = [
        1 => ($totalPartidas >= 1),
        2 => ($sequenciaMaxima >= 5),
        3 => ($melhorPontuacao['matematica'] >= 500),
        4 => ($melhorPontuacao['portugues']  >= 500),
        5 => ($erros === 0 && $acertos >= 5),
        6 => ($userNivel >= 5),
    ];

    $toGrant = [];
    foreach ($candidates as $achId => $earned) {
        if ($earned && !isset($ownedSet[$achId])) {
            $toGrant[] = $achId;
        }
    }

    // ── Grant achievements & fetch their display data ─────────────────────────
    $novasConquistas = [];

    if (!empty($toGrant)) {
        $placeholders = implode(',', array_fill(0, count($toGrant), '?'));
        $stmtDef = $pdo->prepare(
            "SELECT id, nome, descricao, icone
               FROM achievements
              WHERE id IN ($placeholders)"
        );
        $stmtDef->execute($toGrant);
        $definitions = $stmtDef->fetchAll();

        $stmtGrant = $pdo->prepare(
            'INSERT IGNORE INTO user_achievements (user_id, achievement_id, data_conquista)
                  VALUES (:uid, :ach_id, NOW())'
        );

        foreach ($definitions as $def) {
            $stmtGrant->execute([':uid' => $userId, ':ach_id' => $def['id']]);
            $novasConquistas[] = [
                'id'        => (int) $def['id'],
                'nome'      => $def['nome'],
                'descricao' => $def['descricao'],
                'icone'     => $def['icone'],
            ];
        }
    }

} catch (PDOException $e) {
    error_log('[conquistas.php] DB error: ' . $e->getMessage());
    jsonError(500, 'Erro ao processar conquistas. Tente novamente mais tarde.');
}

// ── Respond ───────────────────────────────────────────────────────────────────
echo json_encode([
    'success'          => true,
    'novas_conquistas' => $novasConquistas,
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
