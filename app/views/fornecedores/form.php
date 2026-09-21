<?php
require_once __DIR__ . '/../../helpers/icones.php'; 

$editando = $fornecedor !== null;
?>

<div class="cartao" style="max-width: 640px;">
    <div class="cartao__cabecalho">
        <h2><?= $editando ? 'Editar fornecedor' : 'Novo fornecedor' ?></h2>
    </div>

    <form method="POST" action="fornecedores.php?acao=salvar">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= $fornecedor['id'] ?>">
        <?php endif; ?>

        <div class="campo">
            <label for="nome">Nome / Razão social *</label>
            <input type="text" id="nome" name="nome" required
                   value="<?= htmlspecialchars($fornecedor['nome'] ?? '') ?>">
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="campo">
                    <label for="contato">Contato</label>
                    <input type="text" id="contato" name="contato" placeholder="Telefone ou e-mail"
                           value="<?= htmlspecialchars($fornecedor['contato'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="campo">
                    <label for="cnpj">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00"
                           value="<?= htmlspecialchars($fornecedor['cnpj'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 mt-2">
            <button type="submit" class="btn btn-primario" style="width: auto;">
                <?= icone('salvar', 16) ?> Salvar
            </button>
            <a href="fornecedores.php" class="btn" style="width: auto; background: #eee;">Cancelar</a>
        </div>
    </form>
</div>
