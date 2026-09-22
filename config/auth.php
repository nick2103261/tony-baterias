<?php

function usuarioLogado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigirLogin(): void
{
    if (!usuarioLogado()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function exigirAdmin(): void
{
    exigirLogin();

    if(($_SESSION['usuario_perfil'] ?? null) !== 'admin') {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}
