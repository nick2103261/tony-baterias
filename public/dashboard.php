<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$resumo = (new DashboardController())->resumo($_SESSION['usuario_perfil']);

$paginaAtiva  = 'dashboard';
$tituloPagina = 'Controle';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/dashboard/index.php';
require __DIR__ . '/../app/views/layouts/rodape.php';
