<?php
/**
 * api/get-questions.php — Fetch Random Quiz Questions
 * MathPlay Solutions | Studio Game Over
 *
 * GET  ?jogo=matematica&dificuldade=facil&limit=10
 *
 * Security model
 * ──────────────
 * • Correct answers are NEVER sent to the client.
 * • A short-lived session_token is generated and stored server-side mapping
 *   question_ids → correct answers (+ dica + explicacao for feedback).
 * • The client must present this token to verify-answer.php when answering.
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

// ── Only GET allowed ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError(405, 'Método não permitido. Use GET.');
}

// ── Input validation ──────────────────────────────────────────────────────────
$allowedJogos        = ['matematica', 'portugues'];
$allowedDificuldades = ['facil', 'medio', 'dificil'];

$jogo        = trim($_GET['jogo']        ?? '');
$dificuldade = trim($_GET['dificuldade'] ?? '');
$limit       = (int) ($_GET['limit']    ?? 10);

if (!in_array($jogo, $allowedJogos, true)) {
    jsonError(400, 'Parâmetro "jogo" inválido. Use: ' . implode(', ', $allowedJogos));
}

if (!in_array($dificuldade, $allowedDificuldades, true)) {
    jsonError(400, 'Parâmetro "dificuldade" inválido. Use: ' . implode(', ', $allowedDificuldades));
}

// Clamp limit: between 1 and 50
$limit = max(1, min(50, $limit));

// ── Fetch questions from DB ───────────────────────────────────────────────────
try {
    $pdo = getDB();

    /*
     * We SELECT resposta_correta, dica, and explicacao here but will strip
     * resposta_correta from the client payload — they stay in the session only.
     */
    $sql = 'SELECT id,
                   jogo,
                   dificuldade,
                   pergunta AS enunciado,
                   pergunta,
                   alternativa_a,
                   alternativa_b,
                   alternativa_c,
                   alternativa_d,
                   resposta_correta,
                   dica,
                   explicacao
              FROM questions
             WHERE jogo        = :jogo
               AND dificuldade = :dificuldade
             ORDER BY RAND()
             LIMIT :limit_val';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':jogo',        $jogo,        PDO::PARAM_STR);
    $stmt->bindValue(':dificuldade', $dificuldade, PDO::PARAM_STR);
    $stmt->bindValue(':limit_val',   $limit,       PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[get-questions.php] DB error: ' . $e->getMessage());
    jsonError(500, 'Erro ao buscar perguntas. Tente novamente mais tarde.');
}

if (empty($rows)) {
    jsonError(404, 'Nenhuma pergunta encontrada para os filtros informados.');
}

// ── Build session token ───────────────────────────────────────────────────────
$token = bin2hex(random_bytes(24)); // 48-char hex token

if (!isset($_SESSION['quiz_tokens']) || !is_array($_SESSION['quiz_tokens'])) {
    $_SESSION['quiz_tokens'] = [];
}

// Expire tokens older than 2 hours to avoid session bloat
$now = time();
foreach ($_SESSION['quiz_tokens'] as $tk => $data) {
    if (($now - ($data['created_at'] ?? 0)) > 7200) {
        unset($_SESSION['quiz_tokens'][$tk]);
    }
}

$answerMap = [];
foreach ($rows as $row) {
    $answerMap[(int) $row['id']] = [
        'resposta_correta' => $row['resposta_correta'],
        'dica'             => $row['dica'],
        'explicacao'       => $row['explicacao'],
    ];
}

$_SESSION['quiz_tokens'][$token] = [
    'created_at'  => $now,
    'jogo'        => $jogo,
    'dificuldade' => $dificuldade,
    'user_id'     => (int) $_SESSION['user_id'],
    'answers'     => $answerMap,
];

// ── Build client-safe question list (strip resposta_correta) ──────────────────
$clientQuestions = array_map(static function (array $row): array {
    return [
        'id'            => (int) $row['id'],
        'jogo'          => $row['jogo'],
        'dificuldade'   => $row['dificuldade'],
        'pergunta'      => $row['pergunta'],
        'enunciado'     => $row['pergunta'],
        'alternativa_a' => $row['alternativa_a'],
        'alternativa_b' => $row['alternativa_b'],
        'alternativa_c' => $row['alternativa_c'],
        'alternativa_d' => $row['alternativa_d'],
    ];
}, $rows);

// ── Respond ───────────────────────────────────────────────────────────────────
echo json_encode([
    'success'   => true,
    'token'     => $token,
    'total'     => count($clientQuestions),
    'questions' => $clientQuestions,
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
