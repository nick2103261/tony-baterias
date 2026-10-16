<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$controller = new EstoqueController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['mensagem'] = $controller->registrarMovimentacao($_POST, (int) $_SESSION['usuario_id']);

    redirecionar('estoque.php');
}

$produtos      = (new ProdutoController())->listar();
$fornecedores  = (new FornecedorController())->listar();
$movimentacoes = $controller->listarMovimentacoesRecentes(20);

$paginaAtiva  = 'estoque';
$tituloPagina = 'Estoque';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/estoque/listar.php';
require __DIR__ . '/../app/views/layouts/rodape.php';
