<?php
/**
 * MathPlay Solutions — Application configuration.
 *
 * Set credentials in the hosting provider's environment variables:
 * MATHPLAY_DB_USER, MATHPLAY_DB_PASS,
 * MATHPLAY_FTP_USERNAME, MATHPLAY_FTP_PASSWORD.
 */

define('DB_HOST', 'sql301.byethost5.com');
define('DB_PORT', 3306);
define('DB_NAME', 'b5_42774167_mathplay');
define('DB_USER', getenv('MATHPLAY_DB_USER') ?: '');
define('DB_PASS', getenv('MATHPLAY_DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

define('FTP_SERVER', 'ftpupload.net');
define('FTP_USERNAME', getenv('MATHPLAY_FTP_USERNAME') ?: '');
define('FTP_PASSWORD', getenv('MATHPLAY_FTP_PASSWORD') ?: '');
