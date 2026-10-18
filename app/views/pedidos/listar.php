<div class="pagina-topo">
    <div>
        <h1>Pedidos <span class="pagina-topo__contagem"><?= count($pedidos) ?></span></h1>
    </div>
    <div class="pagina-topo__acoes">
        <a href="pedidos.php?acao=form" class="btn btn-primario"><?= icone('mais', 16) ?> Novo pedido</a>
    </div>
</div>

<?= alertaFlash() ?>

<?php if (empty($pedidos)): ?>
    <div class="estado-vazio">Nenhum pedido registrado. O estoque só baixa quando o pedido é concluído.</div>
<?php else: ?>
    <section class="painel">
        <div class="tabela-wrap">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Pagamento</th>
                        <th class="num">Valor</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td class="cel-principal">#<?= (int) $pedido['id'] ?></td>
                            <td><?= htmlspecialchars($pedido['cliente_nome'] ?? 'Balcão') ?></td>
                            <td><?= htmlspecialchars(FORMAS_PAGAMENTO[$pedido['forma_pagamento'] ?? ''] ?? '—') ?></td>
                            <td class="num">R$ <?= moeda($pedido['valor_total']) ?></td>
                            <td><?= badgeStatus($pedido['status']) ?></td>
                            <td class="suave"><?= date('d/m/Y', strtotime($pedido['criado_em'])) ?></td>
                            <td class="cel-acoes">
                                <div class="acoes">
                                    <a href="pedidos.php?acao=recibo&id=<?= $pedido['id'] ?>" class="acao" title="Ver recibo">
                                        <?= icone('relatorios', 16) ?><span>Recibo</span>
                                    </a>
                                    <?php if ($pedido['status'] === 'pendente'): ?>
                                        <a href="pedidos.php?acao=form&id=<?= $pedido['id'] ?>" class="acao" title="Editar">
                                            <?= icone('editar', 16) ?><span>Editar</span>
                                        </a>
                                        <?= botaoAcao('pedidos.php?acao=status', ['id' => $pedido['id'], 'status' => 'concluido'], 'sucesso', 'Concluir', 'acao--ok', 'Concluir esta venda? O estoque dos itens será baixado.') ?>
                                        <?= botaoAcao('pedidos.php?acao=status', ['id' => $pedido['id'], 'status' => 'cancelado'], 'cancelar', 'Cancelar', 'acao--perigo', 'Cancelar este pedido?') ?>
                                    <?php elseif ($pedido['status'] === 'concluido'): ?>
                                        <?= botaoAcao('pedidos.php?acao=status', ['id' => $pedido['id'], 'status' => 'cancelado'], 'cancelar', 'Cancelar', 'acao--perigo', 'Cancelar esta venda? O estoque baixado será devolvido.') ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
