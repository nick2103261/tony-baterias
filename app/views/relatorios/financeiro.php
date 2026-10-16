<?php $totalPeriodo = (float) $financeiro['total_periodo']; ?>

<div class="pagina-topo">
    <div>
        <h1>Financeiro</h1>
    </div>
</div>

<form method="GET" action="financeiro.php" class="filtro-barra">
    <div class="campo">
        <label for="data_inicio">De</label>
        <input type="date" id="data_inicio" name="data_inicio"
               value="<?= htmlspecialchars($financeiro['data_inicio']) ?>">
    </div>
    <div class="campo">
        <label for="data_fim">Até</label>
        <input type="date" id="data_fim" name="data_fim"
               value="<?= htmlspecialchars($financeiro['data_fim']) ?>">
    </div>
    <button type="submit" class="btn btn-escuro">Aplicar período</button>
</form>

<div class="numeros">
    <div class="numero">
        <div class="numero__rotulo">Total vendido no período</div>
        <div class="numero__valor"><small>R$</small><?= moeda($financeiro['total_periodo']) ?></div>
    </div>
</div>

<section class="painel">
    <div class="painel__topo">
        <h2>Por forma de pagamento</h2>
    </div>

    <?php if (empty($financeiro['formas_pagamento'])): ?>
        <div class="estado-vazio">Nenhuma venda concluída nesse período.</div>
    <?php else: ?>
        <div class="tabela-wrap">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>Forma</th>
                        <th class="num">Total</th>
                        <th class="num">Vendas</th>
                        <th class="num">Participação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($financeiro['formas_pagamento'] as $linha): ?>
                        <?php $pct = $totalPeriodo > 0 ? round(((float) $linha['total'] / $totalPeriodo) * 100) : 0; ?>
                        <tr>
                            <td class="cel-principal"><?= htmlspecialchars(FORMAS_PAGAMENTO[$linha['forma_pagamento'] ?? ''] ?? 'Não informada') ?></td>
                            <td class="num">R$ <?= moeda($linha['total']) ?></td>
                            <td class="num"><?= (int) $linha['qtd_vendas'] ?></td>
                            <td class="num" style="min-width: 150px;">
                                <?= (int) $pct ?>%
                                <div class="barra-fina"><span style="width: <?= (int) $pct ?>%"></span></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
