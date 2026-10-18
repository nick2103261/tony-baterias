<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$controller = new ClienteController();
$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('clientes.php');
        }

        $id = (int) ($_POST['id'] ?? 0) ?: null;
        $resultado = $controller->salvar($_POST, $id);
        $_SESSION['mensagem'] = $resultado;

        if (!$resultado['sucesso']) {
            redirecionar('clientes.php?acao=form' . ($id ? "&id={$id}" : ''));
        }

        redirecionar('clientes.php');

    case 'excluir':
        $controller->excluir((int) ($_GET['id'] ?? 0));
        $_SESSION['mensagem'] = ['sucesso' => true, 'mensagem' => 'Cliente excluído com sucesso.'];

        redirecionar('clientes.php');

    case 'form':
        $cliente = null;
        $historico = [];
        if (isset($_GET['id'])) {
            $cliente = $controller->buscar((int) $_GET['id']);

            if ($cliente === null) {
                redirecionar('clientes.php');
            }

            $historico = $controller->historicoCompras($cliente['id']);
        }

        $paginaAtiva  = 'clientes';
        $tituloPagina = $cliente ? 'Editar cliente' : 'Novo cliente';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/clientes/form.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
        break;

    default:
        $clientes = $controller->listar();

        $paginaAtiva  = 'clientes';
        $tituloPagina = 'Clientes';
        require __DIR__ . '/../app/views/layouts/cabecalho.php';
        require __DIR__ . '/../app/views/clientes/listar.php';
        require __DIR__ . '/../app/views/layouts/rodape.php';
}
