<?php $editando = $usuario !== null; ?>

<div class="pagina-topo">
    <div>
        <p class="pagina-topo__eyebrow"><a href="usuarios.php">&larr; Usuários</a></p>
        <h1><?= $editando ? 'Editar usuário' : 'Novo usuário' ?></h1>
    </div>
</div>

<?= alertaFlash() ?>

<form method="POST" action="usuarios.php?acao=salvar" class="form">
    <?= campoCsrf() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
    <?php endif; ?>

    <section class="bloco-form">
        <div class="campos campos--2">
            <div class="campo campo--cheio">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required
                       value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="perfil">Perfil</label>
                <select id="perfil" name="perfil" required>
                    <option value="vendedor" <?= ($usuario['perfil'] ?? '') === 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                    <option value="admin" <?= ($usuario['perfil'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
            <div class="campo campo--cheio">
                <label for="senha"><?= $editando ? 'Nova senha' : 'Senha' ?></label>
                <input type="password" id="senha" name="senha" minlength="6" autocomplete="new-password"
                       <?= $editando ? 'placeholder="Deixe em branco para manter"' : 'required' ?>>
                <p class="dica">Mínimo de 6 caracteres.</p>
            </div>
        </div>
    </section>

    <div class="acoes-form">
        <button type="submit" class="btn btn-primario">Salvar usuário</button>
        <a href="usuarios.php" class="btn btn-secundario">Cancelar</a>
    </div>
</form>
