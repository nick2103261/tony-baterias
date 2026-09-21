<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (usuarioLogado()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/login.php');
}
exit;
