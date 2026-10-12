<?php
require_once __DIR__ . '/../../helpers/icones.php';

$badgesPorStatus = [
    'pendente'  => ['classe' => 'badge-andamento', 'texto' => 'pendente'],
    'concluido' => ['classe' => 'badge-ok',        'texto' => 'concluído'],
    'cancelado' => ['classe' => 'badge-alerta',    'texto' => 'cancelado'],
];
$badge = $badgesPorStatus[$pedido['status']];

$formasPagamento = [
    'dinheiro'       => 'Dinheiro',
    'pix'            => 'Pix',
    'cartao_credito' => 'Cartão de crédito',
    'cartao_debito'  => 'Cartão de débito',
    'boleto'         => 'Boleto',
];
$formaPagamentoLabel = $formasPagamento[$pedido['forma_pagamento']] ?? 'não informada';
$quantidadeTotal = array_sum(array_column($itens, 'quantidade'));
?>

<style>
    .recibo-cabecalho-impressao { display: none; }

    .recibo-ficha {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;

        padding: 16px 18px;
        margin-bottom: 20px;

        background-color: var(--tony-cinza-fundo);
        border-radius: var(--raio-sutil);
    }

    .recibo-ficha dt {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--tony-cinza-texto);
        margin-bottom: 4px;
    }

    .recibo-ficha dd {
        font-size: 14px;
        font-weight: 600;
        color: var(--tony-preto);
    }

    .recibo-totais {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed var(--tony-cinza-linha);

        display: flex;
        justify-content: flex-end;
    }

    .recibo-totais__tabela {
        min-width: 220px;
    }

    .recibo-totais__linha {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        font-size: 14px;
        color: var(--tony-cinza-texto);
        padding: 4px 0;
    }

    .recibo-totais__linha--final {
        margin-top: 4px;
        padding-top: 10px;
        border-top: 1px solid var(--tony-cinza-linha);
        font-size: 19px;
        font-weight: 700;
        color: var(--tony-preto);
    }

    @media print {
        .menu-lateral, .topo, .no-imprimir { display: none !important; }
        .conteudo { padding: 0 !important; }
        .cartao { box-shadow: none !important; border: 1px solid #ddd !important; max-width: 100% !important; }

        .recibo-cabecalho-impressao {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #000;
        }

        .recibo-cabecalho-impressao strong {
            font-size: 18px;
        }

        .recibo-cabecalho-impressao span {
            font-size: 12px;
            color: #555;
        }

        .recibo-ficha { background-color: transparent; border: 1px solid #ddd; }
    }
</style>

<div class="cartao no-imprimir" style="max-width: 640px;">
    <a href="pedidos.php" class="cartao__acao-secundaria">&larr; Voltar para pedidos</a>
</div>

<div class="cartao" style="max-width: 640px;">
    <div class="recibo-cabecalho-impressao">
        <strong>TONY BATERIAS</strong>
        <span>Recibo não fiscal — comprovante de venda</span>
    </div>

    <div class="cartao__cabecalho">
        <h2>Recibo — Pedido #<?= (int) $pedido['id'] ?></h2>
        <span class="badge <?= $badge['classe'] ?>"><?= $badge['texto'] ?></span>
    </div>

    <dl class="recibo-ficha">
        <div>
            <dt>Data</dt>
            <dd><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></dd>
        </div>
        <div>
            <dt>Cliente</dt>
            <dd><?= htmlspecialchars($pedido['cliente_nome'] ?? 'Balcão') ?></dd>
        </div>
        <div>
            <dt>Forma de pagamento</dt>
            <dd><?= htmlspecialchars($formaPagamentoLabel) ?></dd>
        </div>
        <?php if (!empty($pedido['usuario_nome'])): ?>
            <div>
                <dt>Atendido por</dt>
                <dd><?= htmlspecialchars($pedido['usuario_nome']) ?></dd>
            </div>
        <?php endif; ?>
    </dl>

    <table class="tabela">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Qtd.</th>
                <th>Preço unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['produto_nome']) ?></td>
                    <td><?= (int) $item['quantidade'] ?></td>
                    <td>R$ <?= number_format((float) $item['preco_unitario'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format((float) $item['preco_unitario'] * (int) $item['quantidade'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="recibo-totais">
        <div class="recibo-totais__tabela">
            <div class="recibo-totais__linha">
                <span>Itens</span>
                <span><?= (int) $quantidadeTotal ?></span>
            </div>
            <div class="recibo-totais__linha recibo-totais__linha--final">
                <span>Total</span>
                <span>R$ <?= number_format((float) $pedido['valor_total'], 2, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <?php if (!empty($pedido['observacao'])): ?>
        <p style="margin-top: 16px; font-size: 13px; color: var(--tony-cinza-texto);">
            <strong>Observação:</strong> <?= htmlspecialchars($pedido['observacao']) ?>
        </p>
    <?php endif; ?>

    <div class="no-imprimir mt-3">
        <button type="button" class="btn btn-primario" style="width: auto;" onclick="window.print()">
            <?= icone('imprimir', 16) ?> Imprimir
        </button>
    </div>
</div>