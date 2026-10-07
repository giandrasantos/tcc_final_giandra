<?php
/**
 * auth.php — Authentication Guard
 * MathPlay Solutions | Studio Game Over
 *
 * Provides session management, authentication checks, and user-fetch helpers.
 * Include this file at the top of any page that requires the user to be logged in.
 */

// ---------------------------------------------------------------------------
// Bootstrap session
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------------
// Database connection (provides $pdo)
// ---------------------------------------------------------------------------
require_once dirname(__DIR__) . '/config/database.php';

// ---------------------------------------------------------------------------
// isLoggedIn()
// ---------------------------------------------------------------------------
/**
 * Returns true when a valid user_id is stored in the active session.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// ---------------------------------------------------------------------------
// requireAuth()
// ---------------------------------------------------------------------------
/**
 * Enforces authentication. Redirects to the login page when the current
 * visitor is not logged in, optionally carrying a flash message.
 *
 * @param string $message  Optional message to surface on the login page.
 * @return void
 */
function requireAuth(string $message = ''): void
{
    if (!isLoggedIn()) {
        $redirect = '/login.php';

        if ($message !== '') {
            $redirect .= '?msg=' . urlencode($message);
        }

        // Persist the originally requested URL so login can redirect back
        if (!empty($_SERVER['REQUEST_URI'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }

        header('Location: ' . $redirect);
        exit;
    }
}

// ---------------------------------------------------------------------------
// getCurrentUser()
// ---------------------------------------------------------------------------
/**
 * Fetches fresh user data from the database for the session's user_id.
 * Returns null when no user is logged in or the record no longer exists.
 *
 * Returned array keys (subset — depends on your schema):
 *   id, nome, email, nivel, xp, avatar, created_at
 *
 * @return array|null
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    // $pdo is provided by config/database.php
    $pdo = getDB();

    try {
        $stmt = $pdo->prepare(
            'SELECT id, nome, email, nivel, xp, avatar, pontuacao, created_at
               FROM users
              WHERE id = :id
              LIMIT 1'
        );
        $stmt->execute([':id' => (int) $_SESSION['user_id']]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    } catch (PDOException $e) {
        // Log the error without leaking details to the browser
        error_log('[auth.php] getCurrentUser() failed: ' . $e->getMessage());
        return null;
    }
}

// ---------------------------------------------------------------------------
// Convenience: refresh session cache from DB on every request
// ---------------------------------------------------------------------------
/**
 * Refreshes lightweight session-cached fields (nome, nivel, xp, avatar)
 * from the database. Call this once per page load so the nav always shows
 * up-to-date stats without a full getCurrentUser() query everywhere.
 *
 * @return void
 */
function refreshSessionCache(): void
{
    if (!isLoggedIn()) {
        return;
    }

    $pdo = getDB();

    try {
        $stmt = $pdo->prepare(
            'SELECT nome, nivel, xp, avatar
               FROM users
              WHERE id = :id
              LIMIT 1'
        );
        $stmt->execute([':id' => (int) $_SESSION['user_id']]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $_SESSION['user_nome']   = $row['nome'];
            $_SESSION['user_nivel']  = $row['nivel'];
            $_SESSION['user_xp']     = $row['xp'];
            $_SESSION['user_avatar'] = $row['avatar'];
        }
    } catch (PDOException $e) {
        error_log('[auth.php] refreshSessionCache() failed: ' . $e->getMessage());
    }
}
