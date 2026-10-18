<?php
require_once __DIR__ . '/../config/config.php';

if (usuarioLogado()) {
    redirecionar('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->login($_POST['email'] ?? '', $_POST['senha'] ?? '');
}

require __DIR__ . '/../app/views/auth/login.php';
