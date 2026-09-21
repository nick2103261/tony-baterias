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
?>

<style>
    @media print {
        .menu-lateral, .topo, .no-imprimir { display: none !important; }
        .conteudo { padding: 0 !important; }
        .cartao { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

<div class="cartao no-imprimir" style="max-width: 640px;">
    <a href="pedidos.php" class="cartao__acao-secundaria">&larr; Voltar para pedidos</a>
</div>

<div class="cartao" style="max-width: 640px;">
    <div class="cartao__cabecalho">
        <h2>Recibo — Pedido #<?= (int) $pedido['id'] ?></h2>
        <span class="badge <?= $badge['classe'] ?>"><?= $badge['texto'] ?></span>
    </div>

    <p style="margin-bottom: 4px;"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></p>
    <p style="margin-bottom: 4px;"><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente_nome'] ?? 'Balcão') ?></p>
    <p style="margin-bottom: 16px;"><strong>Forma de pagamento:</strong> <?= htmlspecialchars($formaPagamentoLabel) ?></p>

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

    <div style="text-align: right; font-size: 18px; font-weight: 700; margin-top: 16px;">
        Total: R$ <?= number_format((float) $pedido['valor_total'], 2, ',', '.') ?>
    </div>

    <?php if (!empty($pedido['observacao'])): ?>
        <p style="margin-top: 16px; font-size: 13px; color: var(--tony-cinza-texto);">
            <strong>Observação:</strong> <?= htmlspecialchars($pedido['observacao']) ?>
        </p>
    <?php endif; ?>

    <div class="no-imprimir mt-3">
        <button type="button" class="btn btn-primario" style="width: auto;" onclick="window.print()">Imprimir</button>
    </div>
</div>
