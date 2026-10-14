<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirLogin();

$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/clientes.php');
            exit;
        }

        $controller = new ClienteController();
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;

        $resultado = $controller->salvar($_POST, $id);

        $_SESSION['mensagem'] = [
            'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
            'texto' => $resultado['mensagem'],
        ];

        if (!$resultado['sucesso']) {
            $destino = $id ? "clientes.php?acao=form&id={$id}" : 'clientes.php?acao=form';
            header('Location: ' . BASE_URL . '/' . $destino);
            exit;
        }

        header('Location: ' . BASE_URL . '/clientes.php');
        exit;

    case 'salvar_rapido':
        // Usado pelo formulário de pedido (modal "Novo cliente"): responde JSON, sem redirecionar
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
            exit;
        }

        $controller = new ClienteController();
        $resultado = $controller->salvar($_POST, null);

        if ($resultado['sucesso']) {
            echo json_encode([
                'sucesso' => true,
                'id'      => $resultado['id'],
                'nome'    => trim($_POST['nome'] ?? ''),
            ]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => $resultado['mensagem']]);
        }
        exit;

    case 'excluir':
        if (isset($_GET['id'])) {
            $controller = new ClienteController();
            $controller->excluir((int) $_GET['id']);

            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Cliente excluído com sucesso.'];
        }

        header('Location: ' . BASE_URL . '/clientes.php');
        exit;

    case 'form':
        $controller = new ClienteController();

        $cliente = null;
        $historico = [];
        if (isset($_GET['id'])) {
            $cliente = $controller->buscar((int) $_GET['id']);
            if ($cliente === null) {
                header('Location: ' . BASE_URL . '/clientes.php');
                exit;
            }
            $historico = $controller->historicoCompras((int) $_GET['id']);
        }

        $paginaAtiva  = 'clientes';
        $tituloPagina = $cliente ? 'Editar cliente' : 'Novo cliente';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/clientes/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $controller = new ClienteController();
        $clientes = $controller->listar();

        $paginaAtiva  = 'clientes';
        $tituloPagina = 'Clientes';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/clientes/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
