<?php
require_once __DIR__ . '/../../helpers/icones.php';

$rotulosFormaPagamento = [
    'dinheiro'        => 'Dinheiro',
    'pix'             => 'PIX',
    'cartao_credito'  => 'Cartão de crédito',
    'cartao_debito'   => 'Cartão de débito',
    'boleto'          => 'Boleto',
];
?>

<div class="cartao">
    <form method="GET" action="financeiro.php" class="row g-3" style="align-items: end;">
        <div class="col-md-4">
            <div class="campo">
                <label for="data_inicio">De</label>
                <input type="date" id="data_inicio" name="data_inicio"
                       value="<?= htmlspecialchars($financeiro['data_inicio']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="campo">
                <label for="data_fim">Até</label>
                <input type="date" id="data_fim" name="data_fim"
                       value="<?= htmlspecialchars($financeiro['data_fim']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="campo">
                <button type="submit" class="btn btn-primario" style="width: auto;">
                    <?= icone('filtro', 16) ?> Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<div class="grade-resumo">
    <div class="cartao-resumo">
        <div class="cartao-resumo__icone"><?= icone('cifrao', 18) ?></div>
        <span class="cartao-resumo__rotulo">Total vendido no período</span>
        <span class="cartao-resumo__valor">
            R$ <?= number_format($financeiro['total_periodo'], 2, ',', '.') ?>
        </span>
    </div>
    <div class="cartao-resumo">
        <div class="cartao-resumo__icone"><?= icone('grafico', 18) ?></div>
        <span class="cartao-resumo__rotulo">Ticket médio</span>
        <span class="cartao-resumo__valor">
            R$ <?= number_format($financeiro['ticket_medio'], 2, ',', '.') ?>
        </span>
    </div>
</div>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Por forma de pagamento</h2>
    </div>
    <?php if (empty($financeiro['formas_pagamento'])): ?>
        <div class="estado-vazio">
            <?= icone('cifrao', 32) ?>
            <p>Nenhuma venda concluída nesse período.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Forma de pagamento</th>
                    <th>Total</th>
                    <th>Vendas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($financeiro['formas_pagamento'] as $linha): ?>
                    <tr>
                        <td><?= htmlspecialchars($rotulosFormaPagamento[$linha['forma_pagamento']] ?? $linha['forma_pagamento']) ?></td>
                        <td>R$ <?= number_format($linha['total'], 2, ',', '.') ?></td>
                        <td><?= (int) $linha['qtd_vendas'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>