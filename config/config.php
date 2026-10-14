<?php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'tony_baterias');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Modo de erros: 'production' (defesa/uso real) ou 'development' (programando).
// production: nao mostra erro tecnico na tela, mas registra no log do PHP/Apache.
// development: mostra os erros na tela para facilitar a correcao.
define('APP_ENV', 'production');

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
}

date_default_timezone_set('America/Sao_Paulo');

define('BASE_URL', 'http://localhost/tony-baterias/public');

define('BASE_PATH', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
