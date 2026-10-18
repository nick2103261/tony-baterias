<div class="pagina-topo">
    <div>
        <h1>Relatórios</h1>
    </div>
</div>

<form method="GET" action="relatorios.php" class="filtro-barra">
    <div class="campo">
        <label for="data_inicio">De</label>
        <input type="date" id="data_inicio" name="data_inicio"
               value="<?= htmlspecialchars($relatorio['data_inicio']) ?>">
    </div>
    <div class="campo">
        <label for="data_fim">Até</label>
        <input type="date" id="data_fim" name="data_fim"
               value="<?= htmlspecialchars($relatorio['data_fim']) ?>">
    </div>
    <button type="submit" class="btn btn-escuro">Aplicar período</button>
</form>

<div class="numeros">
    <div class="numero">
        <div class="numero__rotulo">Total vendido no período</div>
        <div class="numero__valor"><small>R$</small><?= moeda($relatorio['total_periodo']) ?></div>
    </div>
</div>

<section class="painel">
    <div class="painel__topo">
        <h2>Produtos mais vendidos</h2>
    </div>
    <?php if (empty($relatorio['produtos'])): ?>
        <div class="estado-vazio">Nenhuma venda concluída nesse período.</div>
    <?php else: ?>
        <div class="tabela-wrap">
            <table class="tabela">
                <thead>
                    <tr>
                        <th></th>
                        <th>Produto</th>
                        <th class="num">Vendidos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($relatorio['produtos'] as $i => $linha): ?>
                        <tr>
                            <td class="rank"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></td>
                            <td class="cel-principal"><?= htmlspecialchars($linha['nome']) ?></td>
                            <td class="num"><?= (int) $linha['total_vendido'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
