<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (usuarioLogado()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    $controller->login($_POST['email'] ?? '', $_POST['senha'] ?? '');
    exit;
}

require __DIR__ . '/../app/views/auth/login.php';
