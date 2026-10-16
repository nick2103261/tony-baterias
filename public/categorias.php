<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$controller = new CategoriaController();
$acao = $_GET['acao'] ?? 'listar';

if ($acao === 'salvar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['mensagem'] = $controller->criar($_POST['nome'] ?? '');

    redirecionar('categorias.php');
}

if ($acao === 'excluir') {
    $controller->excluir((int) ($_GET['id'] ?? 0));
    $_SESSION['mensagem'] = ['sucesso' => true, 'mensagem' => 'Categoria excluída com sucesso.'];

    redirecionar('categorias.php');
}

$categorias = $controller->listar();

$paginaAtiva  = 'produtos';
$tituloPagina = 'Categorias';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/categorias/index.php';
require __DIR__ . '/../app/views/layouts/rodape.php';
