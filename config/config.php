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

// Cookie da sessão: não acessível por JavaScript e não enviado em requisições vindas de outros sites
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

// Erro sem tratamento: grava no log do servidor e mostra uma mensagem simples (sem detalhes técnicos em produção)
set_exception_handler(function (Throwable $erro) {
    error_log((string) $erro);
    http_response_code(500);

    echo APP_ENV === 'development'
        ? '<pre>' . htmlspecialchars((string) $erro) . '</pre>'
        : 'Ocorreu um erro inesperado. Tente novamente em instantes.';
});

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
require_once BASE_PATH . '/app/helpers/seguranca.php';
require_once BASE_PATH . '/app/helpers/icones.php';

function redirecionar(string $pagina): void
{
    header('Location: ' . BASE_URL . '/' . $pagina);
    exit;
}

// Limpa os dados da sessão e troca o identificador (usado no logout e quando o usuário perde o acesso)
function reiniciarSessao(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

// Confere no banco a cada requisição: quem foi excluído ou mudou de perfil é atualizado na hora
function usuarioLogado(): bool
{
    static $resultado = null;

    if (!isset($_SESSION['usuario_id'])) {
        return false;
    }

    if ($resultado === null) {
        $usuario = (new Usuario())->buscarPorId((int) $_SESSION['usuario_id']);

        if ($usuario === null) {
            reiniciarSessao();
            return $resultado = false;
        }

        $_SESSION['usuario_nome']   = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];
        $resultado = true;
    }

    return $resultado;
}

function ehAdmin(): bool
{
    return ($_SESSION['usuario_perfil'] ?? null) === 'admin';
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

    if (!ehAdmin()) {
        redirecionar('dashboard.php');
    }
}

// Todo formulário (POST) precisa do token da sessão; ele impede que outro site dispare ações em nome do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirTokenCsrf();
}
