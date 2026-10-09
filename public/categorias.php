<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirLogin();

$acao = $_GET['acao'] ?? 'listar';

if ($acao === 'salvar') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/categorias.php');
        exit;
    }

    $controller = new CategoriaController();
    $resultado = $controller->criar($_POST['nome'] ?? '');

    $_SESSION['mensagem'] = [
        'tipo'  => $resultado['sucesso'] ? 'sucesso' : 'erro',
        'texto' => $resultado['mensagem'],
    ];

    header('Location: ' . BASE_URL . '/categorias.php');
    exit;
}

if ($acao === 'excluir') {
    if (isset($_GET['id'])) {
        $controller = new CategoriaController();
        $controller->excluir((int) $_GET['id']);

        $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Categoria excluída com sucesso.'];
    }

    header('Location: ' . BASE_URL . '/categorias.php');
    exit;
}

$controller = new CategoriaController();
$categorias = $controller->listar();

$paginaAtiva  = 'produtos';
$tituloPagina = 'Categorias';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/categorias/index.php';
require __DIR__ . '/../app/views/layouts/rodape.php';
