<?php
require_once __DIR__ . '/../../helpers/icones.php'; // idempotente: cabecalho.php já deve ter incluído

$editando = $produto !== null;
?>

<div class="cartao" style="max-width: 720px;">
    <div class="cartao__cabecalho">
        <h2><?= $editando ? 'Editar produto' : 'Novo produto' ?></h2>
    </div>

    <form method="POST" action="produtos.php?acao=salvar">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= $produto['id'] ?>">
        <?php endif; ?>

        <div class="campo">
            <label for="nome">Nome do produto *</label>
            <input type="text" id="nome" name="nome" required
                   value="<?= htmlspecialchars($produto['nome'] ?? '') ?>">
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="campo">
                    <label for="categoria_id">Categoria</label>
                    <select id="categoria_id" name="categoria_id">
                        <option value="">Sem categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>"
                                <?= (isset($produto['categoria_id']) && $produto['categoria_id'] == $categoria['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="campo">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" placeholder="Ex: Heliar, Acdelco"
                           value="<?= htmlspecialchars($produto['marca'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="campo">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="campo">
                    <label for="sku">SKU / Código</label>
                    <input type="text" id="sku" name="sku" placeholder="Opcional"
                           value="<?= htmlspecialchars($produto['sku'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="campo">
                    <label for="preco">Preço (R$) *</label>
                    <input type="text" id="preco" name="preco" required
                           value="<?= isset($produto['preco']) ? number_format((float) $produto['preco'], 2, ',', '') : '' ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="campo">
                    <label for="garantia_meses">Garantia (meses)</label>
                    <input type="number" id="garantia_meses" name="garantia_meses" min="0"
                           value="<?= $produto['garantia_meses'] ?? 0 ?>">
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="campo">
                    <label for="estoque_minimo">Estoque mínimo</label>
                    <input type="number" id="estoque_minimo" name="estoque_minimo" min="0"
                           value="<?= $produto['estoque_minimo'] ?? 0 ?>">
                </div>
            </div>
            <div class="col-md-6">
                <?php if (!$editando): ?>
                    <div class="campo">
                        <label for="estoque_inicial">Estoque inicial</label>
                        <input type="number" id="estoque_inicial" name="estoque_inicial" min="0" value="0">
                    </div>
                <?php else: ?>
                    <div class="campo">
                        <label>Estoque atual</label>
                        <input type="text" value="<?= (int) $produto['estoque_atual'] ?> (ajuste pela tela de Estoque)" disabled>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex gap-3 mt-2">
            <button type="submit" class="btn btn-primario" style="width: auto;">
                <?= icone('salvar', 16) ?> Salvar
            </button>
            <a href="produtos.php" class="btn" style="width: auto; background: #eee;">Cancelar</a>
        </div>
    </form>
</div>
