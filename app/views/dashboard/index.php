<?php $ehAdmin = $_SESSION['usuario_perfil'] === 'admin'; ?>

<div class="pagina-topo">
    <div>
        <h1>Controle</h1>
    </div>
    <div class="pagina-topo__acoes">
        <a href="estoque.php" class="btn btn-secundario">Movimentar estoque</a>
        <a href="pedidos.php?acao=form" class="btn btn-primario"><?= icone('mais', 16) ?> Novo pedido</a>
    </div>
</div>

<?= alertaFlash() ?>

<div class="controle">
    <section class="destaque">
        <div class="destaque__rotulo">
            <span><?= $ehAdmin ? 'Vendido no mês' : 'Valor em estoque' ?></span>
        </div>
        <div class="destaque__valor">
            <small>R$</small><?= moeda($ehAdmin ? $resumo['total_vendas_mes'] : $resumo['valor_estoque']) ?>
        </div>
    </section>

    <dl class="ficha">
        <?php if ($ehAdmin): ?>
            <div class="ficha__linha">
                <dt>Valor parado em estoque</dt>
                <dd><small>R$</small><?= moeda($resumo['valor_estoque']) ?></dd>
            </div>
        <?php endif; ?>
        <div class="ficha__linha">
            <dt>Produtos cadastrados</dt>
            <dd><?= (int) $resumo['total_produtos'] ?></dd>
        </div>
        <?php if (!$ehAdmin): ?>
            <div class="ficha__linha">
                <dt>Pedidos pendentes</dt>
                <dd><?= (int) $resumo['pedidos_pendentes'] ?></dd>
            </div>
        <?php endif; ?>
        <div class="ficha__linha <?= $resumo['estoque_baixo_total'] > 0 ? 'ficha__linha--alerta' : '' ?>">
            <dt>Com estoque baixo</dt>
            <dd><?= (int) $resumo['estoque_baixo_total'] ?></dd>
        </div>
    </dl>

    <div class="controle__esq pilha">
        <section class="painel">
            <div class="painel__topo">
                <h2>Últimos pedidos</h2>
                <a href="pedidos.php" class="link-seta">Ver todos</a>
            </div>

            <?php if (empty($resumo['pedidos_recentes'])): ?>
                <div class="estado-vazio">Nenhum pedido ainda. Quando você registrar a primeira venda, ela aparece aqui.</div>
            <?php else: ?>
                <div class="tabela-wrap">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th class="num">Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resumo['pedidos_recentes'] as $pedido): ?>
                                <tr>
                                    <td class="cel-principal">
                                        <a href="pedidos.php?acao=recibo&id=<?= (int) $pedido['id'] ?>">#<?= (int) $pedido['id'] ?></a>
                                    </td>
                                    <td><?= htmlspecialchars($pedido['cliente_nome'] ?? 'Balcão') ?></td>
                                    <td class="num">R$ <?= moeda($pedido['valor_total']) ?></td>
                                    <td><?= badgeStatus($pedido['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($ehAdmin): ?>
            <section class="painel">
                <div class="painel__topo">
                    <h2>Mais vendidos no mês</h2>
                    <a href="relatorios.php" class="link-seta">Relatórios</a>
                </div>

                <?php if (empty($resumo['mais_vendidos'])): ?>
                    <div class="estado-vazio">Nenhuma venda concluída este mês.</div>
                <?php else: ?>
                    <?php $maior = max(1, (int) $resumo['mais_vendidos'][0]['total_vendido']); ?>
                    <ul class="lista-fina">
                        <?php foreach ($resumo['mais_vendidos'] as $linha): ?>
                            <li>
                                <div class="item-estoque__linha">
                                    <span class="item-estoque__nome"><?= htmlspecialchars($linha['nome']) ?></span>
                                    <span class="item-estoque__qtd"><?= (int) $linha['total_vendido'] ?> un.</span>
                                </div>
                                <div class="barra-fina"><span style="width: <?= round($linha['total_vendido'] / $maior * 100) ?>%"></span></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>

    <div class="controle__dir">
        <section class="painel">
            <div class="painel__topo">
                <h2>Estoque baixo</h2>
                <a href="estoque.php" class="link-seta">Repor</a>
            </div>

            <?php if (empty($resumo['estoque_baixo'])): ?>
                <div class="estado-vazio">Tudo acima do mínimo. Nenhum produto precisa de reposição agora.</div>
            <?php else: ?>
                <ul class="lista-fina">
                    <?php foreach ($resumo['estoque_baixo'] as $produto): ?>
                        <li>
                            <a href="produtos.php?acao=form&id=<?= (int) $produto['id'] ?>" class="item-estoque">
                                <div class="item-estoque__linha">
                                    <span class="item-estoque__nome"><?= htmlspecialchars($produto['nome']) ?></span>
                                    <span class="item-estoque__qtd">
                                        <strong><?= (int) $produto['estoque_atual'] ?></strong> / mín. <?= (int) $produto['estoque_minimo'] ?>
                                    </span>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</div>
