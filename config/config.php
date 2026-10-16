<?php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'tony_baterias');
define('DB_USER', 'root');
define('DB_PASS', '');

// 'production' esconde erros técnicos da tela; 'development' mostra (para programar)
define('APP_ENV', 'production');
ini_set('display_errors', APP_ENV === 'development' ? 1 : 0);

date_default_timezone_set('America/Sao_Paulo');

define('BASE_URL', 'http://localhost/tony-baterias/public');
define('BASE_PATH', dirname(__DIR__));

session_start();

// Carrega models e controllers sozinho quando a classe é usada
spl_autoload_register(function (string $classe) {
    foreach (['models', 'controllers'] as $pasta) {
        $arquivo = BASE_PATH . "/app/$pasta/$classe.php";

        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

require_once __DIR__ . '/BancoDados.php';
require_once BASE_PATH . '/app/helpers/validacoes.php';
require_once BASE_PATH . '/app/helpers/icones.php';

function redirecionar(string $pagina): void
{
    header('Location: ' . BASE_URL . '/' . $pagina);
    exit;
}

function usuarioLogado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigirLogin(): void
{
    if (!usuarioLogado()) {
        redirecionar('login.php');
    }
}

function exigirAdmin(): void
{
    exigirLogin();

    if ($_SESSION['usuario_perfil'] !== 'admin') {
        redirecionar('dashboard.php');
    }
}
