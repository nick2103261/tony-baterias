<?php
$quantidadeTotal = array_sum(array_column($itens, 'quantidade'));
?>

<style>
    .recibo {
        position: relative;
        max-width: 600px;
        padding: 38px 36px 34px;

        background-color: var(--superficie);
        border: 1px solid var(--linha);
        box-shadow: var(--sombra-2);
    }

    .recibo::after {
        content: '';
        position: absolute;
        left: -1px;
        right: -1px;
        bottom: -9px;
        height: 9px;
        background:
            linear-gradient(-45deg, transparent 6px, var(--superficie) 0) 0 0 / 12px 9px repeat-x,
            linear-gradient(45deg, transparent 6px, var(--superficie) 0) 0 0 / 12px 9px repeat-x;
        filter: drop-shadow(0 1px 0 var(--linha));
    }

    .recibo__marca {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 16px;

        padding-bottom: 18px;
        border-bottom: 2px solid var(--tinta);
    }

    .recibo__marca strong {
        font-size: 20px;
    }

    .recibo__marca span {
        font-size: 12.5px;
        color: var(--tinta-3);
        text-align: right;
    }

    .recibo__numero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 22px 0 6px;
    }

    .recibo__numero h1 {
        font-size: 30px;
        font-weight: 500;
    }

    .recibo__ficha {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 24px;
        margin: 20px 0 8px;
    }

    .recibo__ficha div {
        padding: 11px 0;
        border-bottom: 1px dashed var(--linha-forte);
    }

    .recibo__ficha dt {
        font-size: 12px;
        color: var(--tinta-3);
        margin-bottom: 3px;
    }

    .recibo__ficha dd {
        font-size: 14.5px;
        font-weight: 600;
    }

    .recibo__totais {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 2px solid var(--tinta);
    }

    .recibo__itens-qtd {
        font-size: 12px;
        color: var(--tinta-3);
    }

    .recibo__total {
        font-size: 36px;
        font-weight: 700;
        line-height: 1;
    }

    .recibo__total small {
        margin-right: 6px;
        font-size: 0.42em;
        font-weight: 500;
        color: var(--tinta-3);
    }

    .recibo__obs {
        margin-top: 20px;
        padding-top: 14px;
        border-top: 1px dashed var(--linha-forte);
        font-size: 13.5px;
        color: var(--tinta-2);
    }

    .recibo__rodape {
        margin-top: 24px;
        font-size: 11px;
        color: var(--tinta-3);
        text-align: center;
    }

    @media (max-width: 520px) {
        .recibo { padding: 28px 20px 26px; }
        .recibo__ficha { grid-template-columns: 1fr; }
    }

    @media print {
        .recibo { box-shadow: none; border: 1px solid #bbb; max-width: 100%; }
        .recibo::after { display: none; }
    }
</style>

<div class="pagina-topo no-imprimir">
    <div>
        <p class="pagina-topo__eyebrow"><a href="pedidos.php">&larr; Pedidos</a></p>
        <h1>Recibo</h1>
    </div>
    <div class="pagina-topo__acoes">
        <button type="button" class="btn btn-primario" onclick="window.print()">
            <?= icone('imprimir', 16) ?> Imprimir
        </button>
    </div>
</div>

<article class="recibo">
    <div class="recibo__marca">
        <strong>Tony Baterias</strong>
        <span>Comprovante de venda<br>sem valor fiscal</span>
    </div>

    <div class="recibo__numero">
        <h1>Pedido #<?= (int) $pedido['id'] ?></h1>
        <?= badgeStatus($pedido['status']) ?>
    </div>

    <dl class="recibo__ficha">
        <div>
            <dt>Data</dt>
            <dd><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></dd>
        </div>
        <div>
            <dt>Cliente</dt>
            <dd><?= htmlspecialchars($pedido['cliente_nome'] ?? 'Balcão') ?></dd>
        </div>
        <div>
            <dt>Pagamento</dt>
            <dd><?= htmlspecialchars(FORMAS_PAGAMENTO[$pedido['forma_pagamento'] ?? ''] ?? 'Não informada') ?></dd>
        </div>
        <?php if (!empty($pedido['usuario_nome'])): ?>
            <div>
                <dt>Atendido por</dt>
                <dd><?= htmlspecialchars($pedido['usuario_nome']) ?></dd>
            </div>
        <?php endif; ?>
    </dl>

    <table class="tabela tabela--fixa">
        <thead>
            <tr>
                <th>Produto</th>
                <th class="num">Qtd.</th>
                <th class="num">Unit.</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td class="cel-principal"><?= htmlspecialchars($item['produto_nome']) ?></td>
                    <td class="num"><?= (int) $item['quantidade'] ?></td>
                    <td class="num"><?= moeda($item['preco_unitario']) ?></td>
                    <td class="num"><?= moeda((float) $item['preco_unitario'] * (int) $item['quantidade']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="recibo__totais">
        <span class="recibo__itens-qtd"><?= (int) $quantidadeTotal ?> <?= (int) $quantidadeTotal === 1 ? 'item' : 'itens' ?></span>
        <span class="recibo__total"><small>R$</small><?= moeda($pedido['valor_total']) ?></span>
    </div>

    <?php if (!empty($pedido['observacao'])): ?>
        <p class="recibo__obs"><strong>Observação:</strong> <?= htmlspecialchars($pedido['observacao']) ?></p>
    <?php endif; ?>

    <p class="recibo__rodape">Obrigado pela preferência.</p>
</article>
