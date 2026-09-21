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
