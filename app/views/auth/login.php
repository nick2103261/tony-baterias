<?php
$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Entrar · Tony Baterias</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
</head>
<body>
    <div class="entrada">
        <div class="entrada__marca">
            <div class="entrada__logo">
                <img src="assets/img/logo-redonda.jpg" alt="">
                <strong>Tony Baterias</strong>
            </div>

            <div class="entrada__texto">
                <h1>Gerenciamento da Loja.</h1>
                <p>Acesso só para admins e funcionários da loja.</p>
            </div>
        </div>

        <div class="entrada__form">
            <div class="entrada__caixa">
                <h2>Entrar</h2>
                <p>Use o e-mail e a senha que o administrador cadastrou para você.</p>

                <?php if ($erro): ?>
                    <div class="alerta alerta-erro" role="alert">
                        <?= icone('alerta') ?><span><?= htmlspecialchars($erro) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <?= campoCsrf() ?>
                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required autofocus autocomplete="username"
                               placeholder="voce@email.com">
                    </div>
                    <div class="campo">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primario btn-bloco">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
