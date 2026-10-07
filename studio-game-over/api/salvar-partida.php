<?php
/**
 * api/salvar-partida.php — Save a Completed Match & Update Player Progress
 * MathPlay Solutions | Studio Game Over
 *
 * POST  Content-Type: application/json
 * Body: {
 *   jogo:        'matematica' | 'portugues',
 *   dificuldade: 'facil' | 'medio' | 'dificil',
 *   pontuacao:   int  (score for this match),
 *   acertos:     int,
 *   erros:       int,
 *   xp_ganho:    int,
 * }
 *
 * Database writes (in a single transaction)
 * ──────────────────────────────────────────
 * 1. INSERT into matches
 * 2. UPSERT  into progress  (melhor_pontuacao, partidas_jogadas)
 * 3. UPDATE  usuarios       (xp += xp_ganho, pontuacao_total += pontuacao, nivel)
 * 4. Refresh session cache  (xp, nivel)
 *
 * Level thresholds
 * ─────────────────
 *  1 Iniciante  :      0 –   999 XP
 *  2 Aprendiz   :  1 000 – 2 499 XP
 *  3 Avançado   :  2 500 – 4 999 XP
 *  4 Especialista:  5 000 – 9 999 XP
 *  5 Mestre     : 10 000+      XP
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

/**
 * Maps a total XP value to a level number (1-5).
 */
function calcularNivel(int $xp): int
{
    return match (true) {
        $xp >= 10000 => 5,
        $xp >= 5000  => 4,
        $xp >= 2500  => 3,
        $xp >= 1000  => 2,
        default      => 1,
    };
}

/**
 * Maps a level number to its display name.
 */
function nomeDivel(int $nivel): string
{
    return match ($nivel) {
        1 => 'Iniciante',
        2 => 'Aprendiz',
        3 => 'Avançado',
        4 => 'Especialista',
        5 => 'Mestre',
        default => 'Desconhecido',
    };
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
$allowedJogos        = ['matematica', 'portugues'];
$allowedDificuldades = ['facil', 'medio', 'dificil'];

$jogo        = trim((string) ($body['jogo']        ?? ''));
$dificuldade = trim((string) ($body['dificuldade'] ?? ''));
$pontuacao   = (int)          ($body['pontuacao']   ?? -1);
$acertos     = (int)          ($body['acertos']     ?? -1);
$erros       = (int)          ($body['erros']       ?? -1);
$xpGanho     = (int)          ($body['xp_ganho']    ?? -1);

if (!in_array($jogo, $allowedJogos, true)) {
    jsonError(400, 'Campo "jogo" inválido. Use: ' . implode(', ', $allowedJogos));
}

if (!in_array($dificuldade, $allowedDificuldades, true)) {
    jsonError(400, 'Campo "dificuldade" inválido. Use: ' . implode(', ', $allowedDificuldades));
}

if ($pontuacao < 0) {
    jsonError(400, 'Campo "pontuacao" deve ser um inteiro não-negativo.');
}

if ($acertos < 0) {
    jsonError(400, 'Campo "acertos" deve ser um inteiro não-negativo.');
}

if ($erros < 0) {
    jsonError(400, 'Campo "erros" deve ser um inteiro não-negativo.');
}

if ($xpGanho < 0) {
    jsonError(400, 'Campo "xp_ganho" deve ser um inteiro não-negativo.');
}

// ── DB Operations ─────────────────────────────────────────────────────────────
$userId = (int) $_SESSION['user_id'];

try {
    $pdo = getDB();
    $pdo->beginTransaction();

    // 1. Insert match record
    $stmtMatch = $pdo->prepare(
        'INSERT INTO matches (user_id, jogo, dificuldade, pontuacao, acertos, erros, xp_ganho, data_partida)
              VALUES (:user_id, :jogo, :dificuldade, :pontuacao, :acertos, :erros, :xp_ganho, NOW())'
    );
    $stmtMatch->execute([
        ':user_id'     => $userId,
        ':jogo'        => $jogo,
        ':dificuldade' => $dificuldade,
        ':pontuacao'   => $pontuacao,
        ':acertos'     => $acertos,
        ':erros'       => $erros,
        ':xp_ganho'    => $xpGanho,
    ]);

    // 2. Upsert progress row
    $stmtProgress = $pdo->prepare(
        'INSERT INTO progress (user_id, jogo, melhor_pontuacao, partidas, acertos, erros)
              VALUES (:user_id, :jogo, :pontuacao, 1, :acertos, :erros)
         ON DUPLICATE KEY UPDATE
              melhor_pontuacao = IF(:pontuacao2 > melhor_pontuacao, :pontuacao2, melhor_pontuacao),
              partidas = partidas + 1,
              acertos = acertos + :acertos2,
              erros = erros + :erros2'
    );
    $stmtProgress->execute([
        ':user_id'    => $userId,
        ':jogo'       => $jogo,
        ':pontuacao'  => $pontuacao,
        ':acertos'    => $acertos,
        ':erros'      => $erros,
        ':pontuacao2' => $pontuacao,
        ':acertos2'   => $acertos,
        ':erros2'     => $erros,
    ]);

    // 3. Fetch current XP and nivel from DB
    $stmtUser = $pdo->prepare(
        'SELECT xp, nivel FROM users WHERE id = :id LIMIT 1'
    );
    $stmtUser->execute([':id' => $userId]);
    $currentUser = $stmtUser->fetch();

    if (!$currentUser) {
        $pdo->rollBack();
        jsonError(404, 'Usuário não encontrado.');
    }

    $oldNivel  = (int) $currentUser['nivel'];
    $newXp     = (int) $currentUser['xp'] + $xpGanho;
    $newNivel  = calcularNivel($newXp);
    $levelUp   = ($newNivel > $oldNivel);

    // 4. Update users: add XP, add score to total, recalculate level
    $stmtUpdate = $pdo->prepare(
        'UPDATE users
            SET xp        = xp + :xp_ganho,
                pontuacao = pontuacao + :pontuacao,
                nivel     = :nivel
          WHERE id = :id'
    );
    $stmtUpdate->execute([
        ':xp_ganho'  => $xpGanho,
        ':pontuacao' => $pontuacao,
        ':nivel'     => $newNivel,
        ':id'        => $userId,
    ]);

    $pdo->commit();

    // 5. Refresh session cache
    $_SESSION['user_xp']    = $newXp;
    $_SESSION['user_nivel'] = $newNivel;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[salvar-partida.php] DB error: ' . $e->getMessage());
    jsonError(500, 'Erro ao salvar partida. Tente novamente mais tarde.');
}

// ── Respond ───────────────────────────────────────────────────────────────────
echo json_encode([
    'success'        => true,
    'new_xp'         => $newXp,
    'new_nivel'      => $newNivel,
    'novo_nivel_nome'=> nomeDivel($newNivel),
    'level_up'       => $levelUp,
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
