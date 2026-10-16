<div class="pagina-topo">
    <div>
        <h1>Produtos <span class="pagina-topo__contagem"><?= count($produtos) ?></span></h1>
    </div>
    <div class="pagina-topo__acoes">
        <a href="categorias.php" class="btn btn-secundario">Categorias</a>
        <a href="produtos.php?acao=form" class="btn btn-primario"><?= icone('mais', 16) ?> Novo produto</a>
    </div>
</div>

<?= alertaFlash() ?>

<?php if (empty($produtos)): ?>
    <div class="estado-vazio">Nenhum produto cadastrado.</div>
<?php else: ?>
    <section class="painel">
        <div class="tabela-wrap">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Marca</th>
                        <th>SKU</th>
                        <th>Categoria</th>
                        <th class="num">Preço</th>
                        <th>Garantia</th>
                        <th class="num">Estoque</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td class="cel-principal"><?= htmlspecialchars($produto['nome']) ?></td>
                            <td><?= htmlspecialchars($produto['marca'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($produto['sku'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($produto['categoria_nome'] ?? '—') ?></td>
                            <td class="num">R$ <?= moeda($produto['preco']) ?></td>
                            <td><?= $produto['garantia_meses'] > 0 ? $produto['garantia_meses'] . ' meses' : '—' ?></td>
                            <td class="num">
                                <?= (int) $produto['estoque_atual'] ?>
                                <?php if ($produto['estoque_atual'] <= $produto['estoque_minimo']): ?>
                                    <span class="badge badge-alerta">baixo</span>
                                <?php endif; ?>
                            </td>
                            <td class="cel-acoes">
                                <div class="acoes">
                                    <a href="produtos.php?acao=form&id=<?= $produto['id'] ?>" class="acao" title="Editar">
                                        <?= icone('editar', 16) ?><span>Editar</span>
                                    </a>
                                    <a href="produtos.php?acao=excluir&id=<?= $produto['id'] ?>"
                                       class="acao acao--perigo"
                                       title="Excluir"
                                       onclick="return confirm('Excluir este produto?');">
                                        <?= icone('excluir', 16) ?><span>Excluir</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
