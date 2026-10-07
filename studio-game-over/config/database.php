<?php
/**
 * MathPlay Solutions — Database Configuration
 * Studio Game Over
 *
 * Provides a singleton PDO connection to the 'mathplay' database.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'mathplay');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns the singleton PDO database instance.
 *
 * @return PDO
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Tenta fallback com senha 'mysql' caso esteja rodando no Laragon ou ambiente com senha padrão 'mysql'
            try {
                $pdo = new PDO($dsn, DB_USER, 'mysql', $options);
            } catch (PDOException $e2) {
                error_log('[MathPlay DB Error] ' . $e2->getMessage());
                http_response_code(500);
                exit(json_encode([
                    'success' => false,
                    'message' => 'Erro ao conectar ao banco de dados. Verifique o MySQL.',
                ]));
            }
        }
    }

    return $pdo;
}

// Global instance helper
$pdo = getDB();

