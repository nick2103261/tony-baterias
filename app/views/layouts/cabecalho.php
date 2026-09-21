<?php
$paginaAtiva  = $paginaAtiva ?? '';
$tituloPagina = $tituloPagina ?? 'Tony Baterias';

require_once __DIR__ . '/../../helpers/icones.php';

$itensMenu = [
    'dashboard'    => ['label' => 'Dashboard',    'icone' => 'dashboard',    'link' => 'dashboard.php'],
    'produtos'     => ['label' => 'Produtos',     'icone' => 'produtos',     'link' => 'produtos.php'],
    'fornecedores' => ['label' => 'Fornecedores', 'icone' => 'fornecedores', 'link' => 'fornecedores.php'],
    'clientes'     => ['label' => 'Clientes',     'icone' => 'clientes',     'link' => 'clientes.php'],
    'estoque'      => ['label' => 'Estoque',      'icone' => 'estoque',      'link' => 'estoque.php'],
    'pedidos'      => ['label' => 'Pedidos',      'icone' => 'pedidos',      'link' => 'pedidos.php'],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina) ?> - Tony Baterias</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/tables.css">
</head>
<body>
    <div class="layout">
        <aside class="menu-lateral">
            <div class="menu-lateral__marca">
                <img src="assets/img/logo-redonda.jpg" alt="Tony Baterias">
                <span>TONY BATERIAS</span>
            </div>
            <nav class="menu-lateral__menu">
                <?php foreach ($itensMenu as $chave => $item): ?>
                    <a href="<?= $item['link'] ?>"
                       class="menu-lateral__item <?= $paginaAtiva === $chave ? 'menu-lateral__item--ativo' : '' ?>">
                        <?= icone($item['icone']) ?>
                        <span><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
            <a href="logout.php" class="menu-lateral__sair">
                <?= icone('sair') ?> <span>Sair</span>
            </a>
        </aside>

        <div class="area-principal">

            <header class="topo">
                <h1><?= htmlspecialchars($tituloPagina) ?></h1>

                <div class="topo__usuario">
                    <?= icone('pessoa') ?>
                    <span><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                </div>
            </header>

            <main class="conteudo">
