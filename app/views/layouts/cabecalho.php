<?php
$itensMenu = [
    'dashboard'    => ['grupo' => 'Dia a dia', 'label' => 'Controle',     'icone' => 'dashboard',    'link' => 'dashboard.php'],
    'pedidos'      => ['grupo' => 'Dia a dia', 'label' => 'Pedidos',      'icone' => 'pedidos',      'link' => 'pedidos.php'],
    'clientes'     => ['grupo' => 'Dia a dia', 'label' => 'Clientes',     'icone' => 'clientes',     'link' => 'clientes.php'],
    'produtos'     => ['grupo' => 'Dia a dia', 'label' => 'Produtos',     'icone' => 'produtos',     'link' => 'produtos.php'],
    'estoque'      => ['grupo' => 'Dia a dia', 'label' => 'Estoque',      'icone' => 'estoque',      'link' => 'estoque.php'],
    'fornecedores' => ['grupo' => 'Dia a dia', 'label' => 'Fornecedores', 'icone' => 'fornecedores', 'link' => 'fornecedores.php'],
];

if ($_SESSION['usuario_perfil'] === 'admin') {
    $itensMenu['relatorios'] = ['grupo' => 'Gestão', 'label' => 'Relatórios', 'icone' => 'relatorios', 'link' => 'relatorios.php'];
    $itensMenu['financeiro'] = ['grupo' => 'Gestão', 'label' => 'Financeiro', 'icone' => 'cifrao',     'link' => 'financeiro.php'];
    $itensMenu['usuarios']   = ['grupo' => 'Gestão', 'label' => 'Usuários',   'icone' => 'pessoa',     'link' => 'usuarios.php'];
}

$nomeUsuario = $_SESSION['usuario_nome'];
$grupoAtual  = '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($tituloPagina) ?> · Tony Baterias</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/tables.css">
</head>
<body>
    <div class="app">
        <aside class="lateral">
            <a href="dashboard.php" class="lateral__marca">
                <img src="assets/img/logo-redonda.jpg" alt="">
                <div>
                    <strong>Tony Baterias</strong>
                    <small>gestão da loja</small>
                </div>
            </a>

            <nav class="lateral__nav">
                <?php foreach ($itensMenu as $chave => $item): ?>
                    <?php if ($item['grupo'] !== $grupoAtual): ?>
                        <?php $grupoAtual = $item['grupo']; ?>
                        <p class="lateral__grupo"><?= $grupoAtual ?></p>
                    <?php endif; ?>
                    <a href="<?= $item['link'] ?>" class="lateral__item <?= $paginaAtiva === $chave ? 'lateral__item--ativo' : '' ?>">
                        <?= icone($item['icone'], 17) ?>
                        <span><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="lateral__usuario">
                <span class="lateral__avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($nomeUsuario, 0, 1))) ?></span>
                <div class="lateral__quem">
                    <div class="lateral__nome"><?= htmlspecialchars($nomeUsuario) ?></div>
                    <div class="lateral__perfil"><?= $_SESSION['usuario_perfil'] === 'admin' ? 'Administrador' : 'Vendedor' ?></div>
                </div>
                <form method="POST" action="logout.php" class="form-linha">
                    <?= campoCsrf() ?>
                    <button type="submit" class="lateral__sair" title="Sair" aria-label="Sair">
                        <?= icone('sair', 18) ?>
                    </button>
                </form>
            </div>
        </aside>
        <div class="lateral__fundo" id="lateral-fundo"></div>

        <div class="principal">
            <header class="barra-mobile">
                <button type="button" class="barra-mobile__menu" id="abrir-menu" aria-label="Abrir menu">
                    <?= icone('menu', 22) ?>
                </button>
                <span class="barra-mobile__marca">Tony Baterias</span>
                <span class="barra-mobile__pagina"><?= htmlspecialchars($tituloPagina) ?></span>
            </header>

            <main class="conteudo">
