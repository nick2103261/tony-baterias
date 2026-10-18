<?php $editando = $produto !== null; ?>

<div class="pagina-topo">
    <div>
        <p class="pagina-topo__eyebrow"><a href="produtos.php">&larr; Produtos</a></p>
        <h1><?= $editando ? 'Editar produto' : 'Novo produto' ?></h1>
    </div>
</div>

<?= alertaFlash() ?>

<form method="POST" action="produtos.php?acao=salvar" class="form">
    <?= campoCsrf() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">
    <?php endif; ?>

    <section class="bloco-form">
        <div class="campos campos--2">
            <div class="campo campo--cheio">
                <label for="nome">Nome do produto</label>
                <input type="text" id="nome" name="nome" required
                       value="<?= htmlspecialchars($produto['nome'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="categoria_id">Categoria</label>
                <select id="categoria_id" name="categoria_id">
                    <option value="">Sem categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>"
                            <?= ($produto['categoria_id'] ?? null) == $categoria['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca" placeholder="Heliar, Moura, Acdelco…"
                       value="<?= htmlspecialchars($produto['marca'] ?? '') ?>">
            </div>
            <div class="campo campo--cheio">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="3"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
            </div>
        </div>
    </section>

    <section class="bloco-form">
        <div class="campos campos--3">
            <div class="campo">
                <label for="preco">Preço (R$)</label>
                <input type="text" id="preco" name="preco" required inputmode="decimal" placeholder="0,00"
                       value="<?= $editando ? number_format((float) $produto['preco'], 2, ',', '') : '' ?>">
            </div>
            <div class="campo">
                <label for="sku">SKU / código</label>
                <input type="text" id="sku" name="sku"
                       value="<?= htmlspecialchars($produto['sku'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="garantia_meses">Garantia (meses)</label>
                <input type="number" id="garantia_meses" name="garantia_meses" min="0"
                       value="<?= $produto['garantia_meses'] ?? 0 ?>">
            </div>
            <div class="campo">
                <label for="estoque_minimo">Estoque mínimo</label>
                <input type="number" id="estoque_minimo" name="estoque_minimo" min="0"
                       value="<?= $produto['estoque_minimo'] ?? 0 ?>">
            </div>
            <?php if ($editando): ?>
                <div class="campo">
                    <label for="estoque_atual">Estoque atual</label>
                    <input type="text" id="estoque_atual" value="<?= (int) $produto['estoque_atual'] ?>" disabled>
                </div>
            <?php else: ?>
                <div class="campo">
                    <label for="estoque_inicial">Estoque inicial</label>
                    <input type="number" id="estoque_inicial" name="estoque_inicial" min="0" value="0">
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="acoes-form">
        <button type="submit" class="btn btn-primario">Salvar produto</button>
        <a href="produtos.php" class="btn btn-secundario">Cancelar</a>
    </div>
</form>
