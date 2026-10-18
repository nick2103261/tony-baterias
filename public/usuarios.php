<?php
require_once __DIR__ . '/../config/config.php';
exigirAdmin();

$controller = new UsuarioController();
$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('usuarios.php');
        }

        $id = (int) ($_POST['id'] ?? 0) ?: null;
        $resultado = $controller->salvar($_POST, $id);
        $_SESSION['mensagem'] = $resultado;

        if (!$resultado['sucesso']) {
            redirecionar('usuarios.php?acao=form' . ($id ? "&id={$id}" : ''));
        }

        redirecionar('usuarios.php');

    case 'excluir':
        $controller->excluir((int) ($_GET['id'] ?? 0));
        $_SESSION['mensagem'] = ['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso.'];

        redirecionar('usuarios.php');

    case 'form':
        $usuario = null;
        if (isset($_GET['id'])) {
            $usuario = $controller->buscar((int) $_GET['id']);

            if ($usuario === null) {
                redirecionar('usuarios.php');
            }
        }

        $paginaAtiva  = 'usuarios';
        $tituloPagina = $usuario ? 'Editar usuário' : 'Novo usuário';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/usuarios/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $usuarios = $controller->listar();

        $paginaAtiva  = 'usuarios';
        $tituloPagina = 'Usuários';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/usuarios/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
