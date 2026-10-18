<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigirAdmin();

$controller = new AtividadeController();
$atividades = $controller->listarRecentes(50);

$paginaAtiva  = 'atividades';
$tituloPagina = 'Atividades';
require __DIR__ . '/../app/views/layouts/cabecalho.php';
require __DIR__ . '/../app/views/atividades/index.php';
require __DIR__ . '/../app/views/layouts/rodape.php';