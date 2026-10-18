<?php $editando = $fornecedor !== null; ?>

<div class="pagina-topo">
    <div>
        <p class="pagina-topo__eyebrow"><a href="fornecedores.php">&larr; Fornecedores</a></p>
        <h1><?= $editando ? 'Editar fornecedor' : 'Novo fornecedor' ?></h1>
    </div>
</div>

<?= alertaFlash() ?>

<form method="POST" action="fornecedores.php?acao=salvar" class="form">
    <?= campoCsrf() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $fornecedor['id'] ?>">
    <?php endif; ?>

    <section class="bloco-form">
        <div class="campos campos--2">
            <div class="campo campo--cheio">
                <label for="nome">Nome ou razão social</label>
                <input type="text" id="nome" name="nome" required
                       value="<?= htmlspecialchars($fornecedor['nome'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="contato">Contato</label>
                <input type="text" id="contato" name="contato" placeholder="Telefone ou e-mail"
                       value="<?= htmlspecialchars($fornecedor['contato'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00"
                       value="<?= htmlspecialchars($fornecedor['cnpj'] ?? '') ?>">
            </div>
        </div>
    </section>

    <div class="acoes-form">
        <button type="submit" class="btn btn-primario">Salvar fornecedor</button>
        <a href="fornecedores.php" class="btn btn-secundario">Cancelar</a>
    </div>
</form>
