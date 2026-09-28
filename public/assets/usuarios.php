<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirAdmin();

$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/usuarios.php');
            exit;
        }

        $controller = new UsuarioController();
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;

        $resultado = $controller->salvar($_POST, $id);

        $_SESSION['mensagem'] = [
            'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
            'texto' => $resultado['mensagem'],
        ];

        if (!$resultado['sucesso']) {
            $destino = $id ? "usuarios.php?acao=form&id={$id}" : 'usuarios.php?acao=form';
            header('Location: ' . BASE_URL . '/' . $destino);
            exit;
        }

        header('Location: ' . BASE_URL . '/usuarios.php');
        exit;

    case 'excluir':
        if (isset($_GET['id'])) {
            $controller = new UsuarioController();
            $controller->excluir((int) $_GET['id']);

            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Usuário excluído com sucesso.'];
        }

        header('Location: ' . BASE_URL . '/usuarios.php');
        exit;

    case 'form':
        $controller = new UsuarioController();

        $usuario = null;
        if (isset($_GET['id'])) {
            $usuario = $controller->buscar((int) $_GET['id']);
            if ($usuario === null) {
                header('Location: ' . BASE_URL . '/usuarios.php');
                exit;
            }
        }

        $paginaAtiva  = 'usuarios';
        $tituloPagina = $usuario ? 'Editar usuário' : 'Novo usuário';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/usuarios/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $controller = new UsuarioController();
        $usuarios = $controller->listar();

        $paginaAtiva  = 'usuarios';
        $tituloPagina = 'Usuários';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/usuarios/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}