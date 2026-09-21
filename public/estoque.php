<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new EstoqueController();
    $resultado = $controller->registrarMovimentacao($_POST, (int) $_SESSION['usuario_id']);

    $_SESSION['mensagem'] = [
        'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
        'texto' => $resultado['mensagem'],
    ];

    header('Location: ' . BASE_URL . '/estoque.php');
    exit;
}

$controller = new EstoqueController();
$fornecedorController = new FornecedorController();

$produtos = $controller->listarComEstoque();
$fornecedores = $fornecedorController->listar();
$movimentacoes = $controller->listarMovimentacoesRecentes(20);

$paginaAtiva  = 'estoque';
$tituloPagina = 'Estoque';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/estoque/listar.php';
require __DIR__ . '/../app/views/layouts/rodape.php';
