<?php

function tokenCsrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

// Vai dentro de todo <form method="POST">
function campoCsrf(): string
{
    return '<input type="hidden" name="csrf" value="' . tokenCsrf() . '">';
}

function exigirTokenCsrf(): void
{
    $enviado = $_POST['csrf'] ?? '';

    if (!is_string($enviado) || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $enviado)) {
        http_response_code(419);
        exit('Sessão expirada ou requisição inválida. Volte à página anterior, recarregue e tente de novo.');
    }
}
