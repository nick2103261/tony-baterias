<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$controller = new CategoriaController();
$acao = $_GET['acao'] ?? 'listar';

if ($acao === 'salvar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['mensagem'] = $controller->criar(campoTexto($_POST, 'nome'));

    redirecionar('categorias.php');
}

if ($acao === 'excluir') {
    exigirAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirecionar('categorias.php');
    }

    $_SESSION['mensagem'] = $controller->excluir((int) ($_POST['id'] ?? 0));

    redirecionar('categorias.php');
}

$categorias = $controller->listar();

$paginaAtiva  = 'produtos';
$tituloPagina = 'Categorias';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/categorias/index.php';
require __DIR__ . '/../app/views/layouts/rodape.php';
