<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirLogin();

$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/pedidos.php');
            exit;
        }

        $controller = new PedidoController();
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;

        $resultado = $controller->salvar($_POST, $id, (int) $_SESSION['usuario_id']);

        $_SESSION['mensagem'] = [
            'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
            'texto' => $resultado['mensagem'],
        ];

        if (!$resultado['sucesso']) {
            $destino = $id ? "pedidos.php?acao=form&id={$id}" : 'pedidos.php?acao=form';
            header('Location: ' . BASE_URL . '/' . $destino);
            exit;
        }

        header('Location: ' . BASE_URL . '/pedidos.php');
        exit;

    case 'status':
        $id = (int) ($_GET['id'] ?? 0);
        $novoStatus = $_GET['status'] ?? '';

        if ($id > 0 && $novoStatus !== '') {
            $controller = new PedidoController();
            $resultado = $controller->atualizarStatus($id, $novoStatus, (int) $_SESSION['usuario_id']);

            $_SESSION['mensagem'] = [
                'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
                'texto' => $resultado['mensagem'],
            ];
        }

        header('Location: ' . BASE_URL . '/pedidos.php');
        exit;

    case 'recibo':
        $controller = new PedidoController();

        $id = (int) ($_GET['id'] ?? 0);
        $pedido = $controller->buscar($id);

        if ($pedido === null) {
            header('Location: ' . BASE_URL . '/pedidos.php');
            exit;
        }

        $itens = $controller->buscarItens($id);

        $paginaAtiva  = 'pedidos';
        $tituloPagina = "Recibo #{$id}";
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/pedidos/recibo.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    case 'form':
        $controller = new PedidoController();
        $clienteController = new ClienteController();
        $produtoController = new ProdutoController();

        $clientes = $clienteController->listar();
        $produtos = $produtoController->listar();

        $pedido = null;
        $itens = [];
        if (isset($_GET['id'])) {
            $pedido = $controller->buscar((int) $_GET['id']);
            if ($pedido === null) {
                header('Location: ' . BASE_URL . '/pedidos.php');
                exit;
            }
            if ($pedido['status'] !== 'pendente') {
                $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Esse pedido já não está mais pendente e não pode ser editado.'];
                header('Location: ' . BASE_URL . '/pedidos.php');
                exit;
            }
            $itens = $controller->buscarItens((int) $_GET['id']);
        }

        $paginaAtiva  = 'pedidos';
        $tituloPagina = $pedido ? 'Editar pedido' : 'Novo pedido';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/pedidos/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $controller = new PedidoController();
        $pedidos = $controller->listar();

        $paginaAtiva  = 'pedidos';
        $tituloPagina = 'Pedidos';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/pedidos/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
