<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirAdmin();

$controller = new RelatorioController();
$relatorio  = $controller->gerar(
    trim($_GET['data_inicio'] ?? ''),
    trim($_GET['data_fim'] ?? '')
);

$paginaAtiva  = 'relatorios';
$tituloPagina = 'Relatórios';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/relatorios/index.php';
require __DIR__ . '/../app/views/layouts/rodape.php';