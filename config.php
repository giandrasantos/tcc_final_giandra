<?php
/**
 * MathPlay Solutions — Application configuration.
 *
 * Set passwords in the hosting provider's environment variables:
 * MATHPLAY_DB_PASS, MATHPLAY_FTP_USERNAME, MATHPLAY_FTP_PASSWORD.
 */

define('DB_HOST', 'sql301.byethost5.com');
define('DB_PORT', 3306);
define('DB_NAME', 'b5_42774167_mathplay');
define('DB_USER', 'b5_42774167');
define('DB_PASS', 'Senai@474');
define('DB_CHARSET', 'utf8mb4');

define('FTP_SERVER', 'ftpupload.net');
define('FTP_USERNAME', getenv('MATHPLAY_FTP_USERNAME') ?: '');
define('FTP_PASSWORD', getenv('MATHPLAY_FTP_PASSWORD') ?: '');

$scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$appBasePath = preg_replace('~/(?:api|jogos)$~', '', $scriptDirectory);
define('APP_BASE_PATH', rtrim($appBasePath, '/') . '/');
