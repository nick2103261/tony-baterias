<?php
require_once __DIR__ . '/../../helpers/icones.php';

$editando = $usuario !== null;
?>

<div class="cartao" style="max-width: 640px;">
    <div class="cartao__cabecalho">
        <h2><?= $editando ? 'Editar usuário' : 'Novo usuário' ?></h2>
    </div>

    <form method="POST" action="usuarios.php?acao=salvar">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
        <?php endif; ?>

        <div class="campo">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" required
                   value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="campo">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required
                           value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="campo">
                    <label for="perfil">Perfil *</label>
                    <select id="perfil" name="perfil" required>
                        <option value="vendedor" <?= (isset($usuario['perfil']) && $usuario['perfil'] === 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
                        <option value="admin" <?= (isset($usuario['perfil']) && $usuario['perfil'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
            </div>
        </div>

        <?php if (!$editando): ?>
            <div class="campo">
                <label for="senha">Senha *</label>
                <input type="password" id="senha" name="senha" required>
            </div>
        <?php else: ?>
            <p style="font-size: 12px; color: var(--tony-cinza-texto); margin-top: -10px; margin-bottom: 18px;">
                A senha não é alterada por aqui nesta etapa.
            </p>
        <?php endif; ?>

        <div class="d-flex gap-3 mt-2">
            <button type="submit" class="btn btn-primario" style="width: auto;">
                <?= icone('salvar', 16) ?> Salvar
            </button>
            <a href="usuarios.php" class="btn" style="width: auto; background: #eee;">Cancelar</a>
        </div>
    </form>
</div>