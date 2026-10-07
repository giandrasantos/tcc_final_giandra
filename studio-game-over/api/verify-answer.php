<?php
/**
 * api/verify-answer.php — Verify a Quiz Answer Server-Side
 * MathPlay Solutions | Studio Game Over
 *
 * POST  (application/json or form-encoded)
 * Body: { token, question_id, resposta_dada }
 *
 * Security model
 * ──────────────
 * • Correct answers live only in $_SESSION — never exposed to the client.
 * • On a correct answer, the question is removed from the session token so
 *   the same question cannot be replayed.
 * • The token is bound to the authenticated user_id to prevent token-sharing
 *   between sessions.
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

// ── Only POST allowed ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError(405, 'Método não permitido. Use POST.');
}

// ── Parse request body ────────────────────────────────────────────────────────
// Accept both JSON and form-encoded bodies for flexibility
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true);
    if (!is_array($body)) {
        jsonError(400, 'JSON inválido no corpo da requisição.');
    }
} else {
    $body = $_POST;
}

// ── Extract & validate inputs ─────────────────────────────────────────────────
$token        = trim((string) ($body['token']        ?? ''));
$questionId   = (int)          ($body['question_id']  ?? 0);
$respostaDada = strtolower(trim((string) ($body['resposta_dada'] ?? '')));

if ($token === '') {
    jsonError(400, 'Token obrigatório.');
}

if ($questionId <= 0) {
    jsonError(400, 'question_id inválido.');
}

$allowedRespostas = ['a', 'b', 'c', 'd'];
if (!in_array($respostaDada, $allowedRespostas, true)) {
    jsonError(400, 'resposta_dada inválida. Use: a, b, c ou d.');
}

// ── Validate token exists in session ─────────────────────────────────────────
if (
    !isset($_SESSION['quiz_tokens'][$token]) ||
    !is_array($_SESSION['quiz_tokens'][$token])
) {
    jsonError(403, 'Token inválido ou expirado.');
}

$tokenData = &$_SESSION['quiz_tokens'][$token];

// Guard: token must belong to this user
if ((int) ($tokenData['user_id'] ?? 0) !== (int) $_SESSION['user_id']) {
    jsonError(403, 'Token não pertence ao usuário autenticado.');
}

// Guard: token expiry (2 hours)
if ((time() - ($tokenData['created_at'] ?? 0)) > 7200) {
    unset($_SESSION['quiz_tokens'][$token]);
    jsonError(403, 'Token expirado. Inicie um novo jogo.');
}

// Guard: question must be in token
if (!array_key_exists($questionId, $tokenData['answers'])) {
    jsonError(404, 'Pergunta não encontrada neste token ou já respondida.');
}

// ── Evaluate the answer ───────────────────────────────────────────────────────
$correctData   = $tokenData['answers'][$questionId];
$correctAnswer = strtolower((string) $correctData['resposta_correta']);
$dica          = (string) ($correctData['dica']       ?? '');
$explicacao    = (string) ($correctData['explicacao'] ?? '');

$isCorrect = ($respostaDada === $correctAnswer);

// Remove this question from session (one-shot: prevents replay)
unset($tokenData['answers'][$questionId]);

// If no questions remain, clean up the token entirely
if (empty($tokenData['answers'])) {
    unset($_SESSION['quiz_tokens'][$token]);
}

// ── Respond ───────────────────────────────────────────────────────────────────
echo json_encode([
    'success'        => true,
    'correct'        => $isCorrect,
    'correct_answer' => $correctAnswer,
    'dica'           => $dica,
    'explicacao'     => $explicacao,
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
