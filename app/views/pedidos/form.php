<?php
require_once __DIR__ . '/../../helpers/icones.php'; 

$editando = $pedido !== null;

$formasPagamento = [
    'dinheiro'       => 'Dinheiro',
    'pix'            => 'Pix',
    'cartao_credito' => 'Cartão de crédito',
    'cartao_debito'  => 'Cartão de débito',
    'boleto'         => 'Boleto',
];
?>

<div class="cartao" style="max-width: 760px;">
    <div class="cartao__cabecalho">
        <h2><?= $editando ? "Editar pedido #{$pedido['id']}" : 'Novo pedido' ?></h2>
    </div>

    <form method="POST" action="pedidos.php?acao=salvar" id="form-pedido">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="campo">
                    <label for="cliente_id">Cliente</label>
                    <select id="cliente_id" name="cliente_id">
                        <option value="">Balcão (sem cliente)</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id'] ?>"
                                <?= (isset($pedido['cliente_id']) && $pedido['cliente_id'] == $cliente['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cliente['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="campo">
                    <label for="forma_pagamento">Forma de pagamento</label>
                    <select id="forma_pagamento" name="forma_pagamento">
                        <option value="">Não definida</option>
                        <?php foreach ($formasPagamento as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= (($pedido['forma_pagamento'] ?? '') === $valor) ? 'selected' : '' ?>>
                                <?= $rotulo ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="campo">
            <label for="observacao">Observação</label>
            <input type="text" id="observacao" name="observacao"
                   value="<?= htmlspecialchars($pedido['observacao'] ?? '') ?>">
        </div>

        <div class="campo">
            <label>Itens do pedido *</label>
            <div id="lista-itens"></div>
            <button type="button" class="btn" style="width: auto; background: #eee; margin-top: 4px;" id="btn-add-item">
                <?= icone('mais', 16) ?> Adicionar item
            </button>
        </div>

        <div style="text-align: right; font-size: 18px; font-weight: 700; margin: 16px 0;">
            Total: R$ <span id="valor-total">0,00</span>
        </div>

        <div class="d-flex gap-3 mt-2">
            <button type="submit" class="btn btn-primario" style="width: auto;">
                <?= icone('salvar', 16) ?> Salvar pedido
            </button>
            <a href="pedidos.php" class="btn" style="width: auto; background: #eee;">Cancelar</a>
        </div>
    </form>
</div>

<span id="icone-excluir-template" style="display:none;"><?= icone('excluir', 16) ?></span>

<script>
const PRODUTOS = <?= json_encode(array_map(fn($p) => [
    'id'    => (int) $p['id'],
    'nome'  => $p['nome'],
    'preco' => (float) $p['preco'],
], $produtos)) ?>;

const ITENS_INICIAIS = <?= json_encode(array_map(fn($i) => [
    'produto_id' => (int) $i['produto_id'],
    'quantidade' => (int) $i['quantidade'],
], $itens)) ?>;

const lista = document.getElementById('lista-itens');
const iconeExcluir = document.getElementById('icone-excluir-template').innerHTML;

function formatarMoeda(valor) {
    return valor.toFixed(2).replace('.', ',');
}

function criarLinha(itemInicial) {
    const linha = document.createElement('div');
    linha.className = 'linha-item-pedido';
    linha.style.cssText = 'display:flex; gap:12px; align-items:flex-end; margin-bottom:10px;';

    const opcoesProduto = PRODUTOS.map(p =>
        `<option value="${p.id}" data-preco="${p.preco}">${p.nome} — R$ ${formatarMoeda(p.preco)}</option>`
    ).join('');

    linha.innerHTML = `
        <div class="campo" style="flex:2; margin-bottom:0;">
            <select name="produto_id[]" class="select-produto" required>
                <option value="">Selecione um produto...</option>
                ${opcoesProduto}
            </select>
        </div>
        <div class="campo" style="flex:1; margin-bottom:0;">
            <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="1" required>
        </div>
        <div style="flex:1; padding-bottom:12px; font-weight:600; white-space:nowrap;">
            R$ <span class="subtotal-item">0,00</span>
        </div>
        <button type="button" class="btn-remover-item"
                style="background:none; border:none; cursor:pointer; padding-bottom:12px; color: var(--tony-erro);">
            ${iconeExcluir}
        </button>
    `;

    const selectProduto   = linha.querySelector('.select-produto');
    const inputQuantidade = linha.querySelector('.input-quantidade');
    const subtotalSpan    = linha.querySelector('.subtotal-item');
    const btnRemover      = linha.querySelector('.btn-remover-item');

    function atualizarSubtotal() {
        const opcao = selectProduto.selectedOptions[0];
        const preco = opcao ? parseFloat(opcao.dataset.preco || 0) : 0;
        const quantidade = parseInt(inputQuantidade.value || 0, 10);
        subtotalSpan.textContent = formatarMoeda(preco * quantidade);
        atualizarTotal();
    }

    selectProduto.addEventListener('change', atualizarSubtotal);
    inputQuantidade.addEventListener('input', atualizarSubtotal);
    btnRemover.addEventListener('click', () => {
        linha.remove();
        atualizarTotal();
    });

    if (itemInicial) {
        selectProduto.value = itemInicial.produto_id;
        inputQuantidade.value = itemInicial.quantidade;
    }

    lista.appendChild(linha);
    atualizarSubtotal();
}

function atualizarTotal() {
    let total = 0;
    const linhas = document.querySelectorAll('.linha-item-pedido');
    for (let i = 0; i < linhas.length; i++) {
        const linha = linhas[i];
        const opcao = linha.querySelector('.select-produto').selectedOptions[0];
        const preco = opcao ? parseFloat(opcao.dataset.preco || 0) : 0;
        const quantidade = parseInt(linha.querySelector('.input-quantidade').value || 0, 10);
        total += preco * quantidade;
    }
    document.getElementById('valor-total').textContent = formatarMoeda(total);
}

document.getElementById('btn-add-item').addEventListener('click', () => criarLinha());

if (ITENS_INICIAIS.length > 0) {
    ITENS_INICIAIS.forEach(item => criarLinha(item));
} else {
    criarLinha();
}

document.getElementById('form-pedido').addEventListener('submit', (evento) => {
    if (document.querySelectorAll('.linha-item-pedido').length === 0) {
        evento.preventDefault();
        alert('Adicione ao menos um item ao pedido.');
    }
});
</script>
