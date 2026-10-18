<?php
$editando = $pedido !== null;

// Uma linha de item (usada nos itens já salvos e no modelo para "Adicionar item")
function linhaItem(array $produtos, int $produtoId = 0, int $quantidade = 1): string
{
    $opcoes = '';
    foreach ($produtos as $p) {
        $opcoes .= '<option value="' . $p['id'] . '" data-preco="' . $p['preco'] . '"'
            . ($p['id'] == $produtoId ? ' selected' : '') . '>'
            . htmlspecialchars($p['nome']) . ' — R$ ' . moeda($p['preco']) . '</option>';
    }

    return '<div class="linha-item-pedido">
        <div class="campo item-produto">
            <select name="produto_id[]" aria-label="Produto">
                <option value="">Selecione um produto…</option>' . $opcoes . '
            </select>
        </div>
        <div class="campo item-qtd">
            <input type="number" name="quantidade[]" min="1" value="' . $quantidade . '" aria-label="Quantidade">
        </div>
        <div class="subtotal-celula">R$ <span class="subtotal-item">0,00</span></div>
        <button type="button" class="btn-remover-item" aria-label="Remover item" title="Remover item">'
            . icone('excluir', 16) . '</button>
    </div>';
}
?>

<div class="pagina-topo">
    <div>
        <p class="pagina-topo__eyebrow"><a href="pedidos.php">&larr; Pedidos</a></p>
        <h1><?= $editando ? "Editar pedido #{$pedido['id']}" : 'Novo pedido' ?></h1>
    </div>
</div>

<?= alertaFlash() ?>

<form method="POST" action="pedidos.php?acao=salvar" id="form-pedido" class="form">
    <?= campoCsrf() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
    <?php endif; ?>

    <section class="bloco-form">
        <div class="campos campos--2">
            <div class="campo">
                <label for="cliente_id">Cliente</label>
                <div class="campo-com-acao">
                    <select id="cliente_id" name="cliente_id">
                        <option value="">Balcão (sem cliente)</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id'] ?>"
                                <?= ($pedido['cliente_id'] ?? null) == $cliente['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cliente['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="clientes.php?acao=form" class="btn btn-secundario" title="Cadastrar novo cliente">
                        <?= icone('mais', 16) ?> Novo
                    </a>
                </div>
            </div>
            <div class="campo">
                <label for="forma_pagamento">Forma de pagamento</label>
                <select id="forma_pagamento" name="forma_pagamento">
                    <option value="">Ainda não definida</option>
                    <?php foreach (FORMAS_PAGAMENTO as $valor => $rotulo): ?>
                        <option value="<?= $valor ?>" <?= (($pedido['forma_pagamento'] ?? '') === $valor) ? 'selected' : '' ?>>
                            <?= $rotulo ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo campo--cheio">
                <label for="observacao">Observação</label>
                <input type="text" id="observacao" name="observacao"
                       value="<?= htmlspecialchars($pedido['observacao'] ?? '') ?>">
            </div>
        </div>
    </section>

    <section class="bloco-form">
        <div class="itens-pedido__cab" aria-hidden="true">
            <span>Produto</span>
            <span>Qtd.</span>
            <span>Subtotal</span>
            <span></span>
        </div>

        <div id="lista-itens">
            <?php foreach ($itens ?: [['produto_id' => 0, 'quantidade' => 1]] as $item): ?>
                <?= linhaItem($produtos, (int) $item['produto_id'], (int) $item['quantidade']) ?>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-secundario" id="btn-add-item">
            <?= icone('mais', 16) ?> Adicionar item
        </button>

        <div class="total-pedido">
            <span class="total-pedido__rotulo">Total do pedido</span>
            <span class="total-pedido__valor"><small>R$</small><span id="valor-total">0,00</span></span>
        </div>
    </section>

    <div class="acoes-form">
        <button type="submit" class="btn btn-primario">Salvar pedido</button>
        <a href="pedidos.php" class="btn btn-secundario">Cancelar</a>
    </div>
</form>

<template id="modelo-item"><?= linhaItem($produtos) ?></template>

<script>
// Soma o subtotal de cada linha e o total do pedido
function recalcular() {
    let total = 0;
    document.querySelectorAll('.linha-item-pedido').forEach(linha => {
        const preco = linha.querySelector('select').selectedOptions[0].dataset.preco || 0;
        const subtotal = preco * (linha.querySelector('input').value || 0);
        linha.querySelector('.subtotal-item').textContent = subtotal.toFixed(2).replace('.', ',');
        total += subtotal;
    });
    document.getElementById('valor-total').textContent = total.toFixed(2).replace('.', ',');
}

document.getElementById('form-pedido').addEventListener('input', recalcular);

document.getElementById('btn-add-item').addEventListener('click', () => {
    document.getElementById('lista-itens').append(document.getElementById('modelo-item').content.cloneNode(true));
    recalcular();
});

document.getElementById('lista-itens').addEventListener('click', e => {
    if (e.target.closest('.btn-remover-item')) {
        e.target.closest('.linha-item-pedido').remove();
        recalcular();
    }
});

recalcular();
</script>
