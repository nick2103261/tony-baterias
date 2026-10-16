<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$controller = new PedidoController();
$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('pedidos.php');
        }

        $id = (int) ($_POST['id'] ?? 0) ?: null;
        $resultado = $controller->salvar($_POST, $id, (int) $_SESSION['usuario_id']);
        $_SESSION['mensagem'] = $resultado;

        if (!$resultado['sucesso']) {
            redirecionar('pedidos.php?acao=form' . ($id ? "&id={$id}" : ''));
        }

        redirecionar('pedidos.php');

    case 'status':
        $_SESSION['mensagem'] = $controller->atualizarStatus((int) ($_GET['id'] ?? 0), $_GET['status'] ?? '', (int) $_SESSION['usuario_id']);

        redirecionar('pedidos.php');

    case 'recibo':
        $id = (int) ($_GET['id'] ?? 0);
        $pedido = $controller->buscar($id);

        if ($pedido === null) {
            redirecionar('pedidos.php');
        }

        $itens = $controller->buscarItens($id);

        $paginaAtiva  = 'pedidos';
        $tituloPagina = "Recibo #{$id}";
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/pedidos/recibo.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    case 'form':
        $pedido = null;
        $itens = [];

        if (isset($_GET['id'])) {
            $pedido = $controller->buscar((int) $_GET['id']);

            if ($pedido === null) {
                redirecionar('pedidos.php');
            }

            if ($pedido['status'] !== 'pendente') {
                $_SESSION['mensagem'] = ['sucesso' => false, 'mensagem' => 'Esse pedido já não está mais pendente e não pode ser editado.'];
                redirecionar('pedidos.php');
            }

            $itens = $controller->buscarItens($pedido['id']);
        }

        $clientes = (new ClienteController())->listar();
        $produtos = (new ProdutoController())->listar();

        $paginaAtiva  = 'pedidos';
        $tituloPagina = $pedido ? 'Editar pedido' : 'Novo pedido';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/pedidos/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $pedidos = $controller->listar();

        $paginaAtiva  = 'pedidos';
        $tituloPagina = 'Pedidos';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/pedidos/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
