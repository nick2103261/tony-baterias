<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirAdmin();

$controller = new RelatorioController();
$financeiro = $controller->financeiro(
    trim($_GET['data_inicio'] ?? ''),
    trim($_GET['data_fim'] ?? '')
);

$paginaAtiva  = 'financeiro';
$tituloPagina = 'Financeiro';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/relatorios/financeiro.php';
require __DIR__ . '/../app/views/layouts/rodape.php';