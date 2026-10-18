<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$controller = new ProdutoController();
$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('produtos.php');
        }

        $id = (int) ($_POST['id'] ?? 0) ?: null;
        $resultado = $controller->salvar($_POST, $id, (int) $_SESSION['usuario_id']);
        $_SESSION['mensagem'] = $resultado;

        if (!$resultado['sucesso']) {
            redirecionar('produtos.php?acao=form' . ($id ? "&id={$id}" : ''));
        }

        redirecionar('produtos.php');

    case 'excluir':
        $controller->excluir((int) ($_GET['id'] ?? 0));
        $_SESSION['mensagem'] = ['sucesso' => true, 'mensagem' => 'Produto excluído com sucesso.'];

        redirecionar('produtos.php');

    case 'form':
        $categorias = (new CategoriaController())->listar();

        $produto = null;
        if (isset($_GET['id'])) {
            $produto = $controller->buscar((int) $_GET['id']);

            if ($produto === null) {
                redirecionar('produtos.php');
            }
        }

        $paginaAtiva  = 'produtos';
        $tituloPagina = $produto ? 'Editar produto' : 'Novo produto';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/produtos/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $produtos = $controller->listar();

        $paginaAtiva  = 'produtos';
        $tituloPagina = 'Produtos';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/produtos/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
