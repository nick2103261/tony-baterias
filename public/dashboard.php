<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirLogin();

$controller = new DashboardController();
$resumo = $controller->resumo();

$paginaAtiva  = 'dashboard';
$tituloPagina = 'Dashboard';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/dashboard/index.php';
require __DIR__ . '/../app/views/layouts/rodape.php';
