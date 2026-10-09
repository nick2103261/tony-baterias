<?php require_once __DIR__ . '/../../helpers/icones.php'; ?>

<div class="cartao">
    <form method="GET" action="relatorios.php" class="row g-3" style="align-items: end;">
        <div class="col-md-4">
            <div class="campo">
                <label for="data_inicio">De</label>
                <input type="date" id="data_inicio" name="data_inicio"
                       value="<?= htmlspecialchars($relatorio['data_inicio']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="campo">
                <label for="data_fim">Até</label>
                <input type="date" id="data_fim" name="data_fim"
                       value="<?= htmlspecialchars($relatorio['data_fim']) ?>">
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
        <div class="cartao-resumo__icone"><?= icone('grafico', 18) ?></div>
        <span class="cartao-resumo__rotulo">Total vendido no período</span>
        <span class="cartao-resumo__valor">
            R$ <?= number_format($relatorio['total_periodo'], 2, ',', '.') ?>
        </span>
    </div>
</div>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Produtos mais vendidos</h2>
    </div>
    <?php if (empty($relatorio['produtos'])): ?>
        <div class="estado-vazio">
            <?= icone('produtos', 32) ?>
            <p>Nenhuma venda concluída nesse período.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade vendida</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatorio['produtos'] as $linha): ?>
                    <tr>
                        <td><?= htmlspecialchars($linha['nome']) ?></td>
                        <td><?= (int) $linha['total_vendido'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Clientes que mais compram</h2>
    </div>
    <?php if (empty($relatorio['clientes'])): ?>
        <div class="estado-vazio">
            <?= icone('clientes', 32) ?>
            <p>Nenhum cliente com compra concluída nesse período.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Total comprado</th>
                    <th>Pedidos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatorio['clientes'] as $linha): ?>
                    <tr>
                        <td><?= htmlspecialchars($linha['nome']) ?></td>
                        <td>R$ <?= number_format($linha['total_comprado'], 2, ',', '.') ?></td>
                        <td><?= (int) $linha['qtd_pedidos'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>