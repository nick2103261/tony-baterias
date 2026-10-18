<?php
require_once __DIR__ . '/../config/config.php';

// Só sai por formulário (POST com token): um link em outro site não consegue deslogar o usuário
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('dashboard.php');
}

(new AuthController())->logout();
