<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$controller = new FornecedorController();
$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('fornecedores.php');
        }

        $id = (int) ($_POST['id'] ?? 0) ?: null;
        $resultado = $controller->salvar($_POST, $id);
        $_SESSION['mensagem'] = $resultado;

        if (!$resultado['sucesso']) {
            redirecionar('fornecedores.php?acao=form' . ($id ? "&id={$id}" : ''));
        }

        redirecionar('fornecedores.php');

    case 'excluir':
        exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('fornecedores.php');
        }

        $_SESSION['mensagem'] = $controller->excluir((int) ($_POST['id'] ?? 0));

        redirecionar('fornecedores.php');

    case 'form':
        $fornecedor = null;
        if (isset($_GET['id'])) {
            $fornecedor = $controller->buscar((int) $_GET['id']);

            if ($fornecedor === null) {
                redirecionar('fornecedores.php');
            }
        }

        $paginaAtiva  = 'fornecedores';
        $tituloPagina = $fornecedor ? 'Editar fornecedor' : 'Novo fornecedor';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/fornecedores/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $fornecedores = $controller->listar();

        $paginaAtiva  = 'fornecedores';
        $tituloPagina = 'Fornecedores';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/fornecedores/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
