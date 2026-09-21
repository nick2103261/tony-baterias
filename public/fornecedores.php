<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirLogin();

$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/fornecedores.php');
            exit;
        }

        $controller = new FornecedorController();
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;

        $resultado = $controller->salvar($_POST, $id);

        $_SESSION['mensagem'] = [
            'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
            'texto' => $resultado['mensagem'],
        ];

        if (!$resultado['sucesso']) {
            $destino = $id ? "fornecedores.php?acao=form&id={$id}" : 'fornecedores.php?acao=form';
            header('Location: ' . BASE_URL . '/' . $destino);
            exit;
        }

        header('Location: ' . BASE_URL . '/fornecedores.php');
        exit;

    case 'excluir':
        if (isset($_GET['id'])) {
            $controller = new FornecedorController();
            $controller->excluir((int) $_GET['id']);

            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Fornecedor excluído com sucesso.'];
        }

        header('Location: ' . BASE_URL . '/fornecedores.php');
        exit;

    case 'form':
        $controller = new FornecedorController();

        $fornecedor = null;
        if (isset($_GET['id'])) {
            $fornecedor = $controller->buscar((int) $_GET['id']);
            if ($fornecedor === null) {
                header('Location: ' . BASE_URL . '/fornecedores.php');
                exit;
            }
        }

        $paginaAtiva  = 'fornecedores';
        $tituloPagina = $fornecedor ? 'Editar fornecedor' : 'Novo fornecedor';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/fornecedores/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $controller = new FornecedorController();
        $fornecedores = $controller->listar();

        $paginaAtiva  = 'fornecedores';
        $tituloPagina = 'Fornecedores';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/fornecedores/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
