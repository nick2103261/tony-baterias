<?php
require_once __DIR__ . '/../../helpers/icones.php';

$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tony Baterias</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
</head>
<body>
    <div class="tela-login">
        <div class="cartao-login">
            <img src="assets/img/logo-redonda.jpg" alt="Logo Tony Baterias">
            <div class="cartao-login__marca">
                <h1><span class="baterias">LOGIN</span></h1>
                <p>Gestão da Loja</p>
            </div>
            
            <?php if ($erro): ?>
                <div class="alerta alerta-erro">
                    <?= icone('alerta') ?> <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autofocus placeholder="seu.email@exemplo.com">
                </div>
                <div class="campo">
                    <label for="senha">Teste kkkkkj</label>
                    <input type="password" id="senha" name="senha" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primario">
                    <?= icone('entrar', 16) ?> Entrar
                </button>
            </form>
        </div>
    </div>
</body>
</html>