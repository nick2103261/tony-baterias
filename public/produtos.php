<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirLogin();

$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/produtos.php');
            exit;
        }

        $controller = new ProdutoController();
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;

        $resultado = $controller->salvar($_POST, $id, (int) $_SESSION['usuario_id']);

        $_SESSION['mensagem'] = [
            'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
            'texto' => $resultado['mensagem'],
        ];

        // Se deu erro de validação, volta para o formulário (mantendo o contexto de edição)
        if (!$resultado['sucesso']) {
            $destino = $id ? "produtos.php?acao=form&id={$id}" : 'produtos.php?acao=form';
            header('Location: ' . BASE_URL . '/' . $destino);
            exit;
        }

        header('Location: ' . BASE_URL . '/produtos.php');
        exit;

    case 'excluir':
        if (isset($_GET['id'])) {
            $controller = new ProdutoController();
            $controller->excluir((int) $_GET['id']);

            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Produto excluído com sucesso.'];
        }

        header('Location: ' . BASE_URL . '/produtos.php');
        exit;

    case 'form':
        $controller = new ProdutoController();
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->listarTodas();

        $produto = null;
        if (isset($_GET['id'])) {
            $produto = $controller->buscar((int) $_GET['id']);
            if ($produto === null) {
                header('Location: ' . BASE_URL . '/produtos.php');
                exit;
            }
        }

        $paginaAtiva  = 'produtos';
        $tituloPagina = $produto ? 'Editar produto' : 'Novo produto';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/produtos/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $controller = new ProdutoController();
        $produtos = $controller->listar();

        $paginaAtiva  = 'produtos';
        $tituloPagina = 'Produtos';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/produtos/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
