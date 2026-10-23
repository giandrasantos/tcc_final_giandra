<?php
/**
 * MathPlay Solutions — Database Configuration
 * Studio Game Over
 *
 * Provides a singleton PDO connection to the 'mathplay' database.
 */

require_once dirname(__DIR__) . '/config.php';

/**
 * Returns the singleton PDO database instance.
 *
 * @return PDO
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        if (DB_USER === '' || DB_PASS === '') {
            error_log('[MathPlay DB Error] Database credentials are not configured.');
            http_response_code(500);
            exit(json_encode([
                'success' => false,
                'message' => 'Erro ao conectar ao banco de dados. Verifique a configuração.',
            ], JSON_UNESCAPED_UNICODE));
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
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
            error_log('[MathPlay DB Error] ' . $e->getMessage());
            http_response_code(500);
            exit(json_encode([
                'success' => false,
                'message' => 'Erro ao conectar ao banco de dados. Verifique o MySQL.',
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    return $pdo;
}

// Global instance helper
$pdo = getDB();
