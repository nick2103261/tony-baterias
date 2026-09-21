<?php
require_once __DIR__ . '/../../helpers/icones.php'; 

$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);

$badgesPorStatus = [
    'pendente'  => ['classe' => 'badge-andamento', 'texto' => 'pendente'],
    'concluido' => ['classe' => 'badge-ok',        'texto' => 'concluído'],
    'cancelado' => ['classe' => 'badge-alerta',    'texto' => 'cancelado'],
];

$formasPagamento = [
    'dinheiro'       => 'Dinheiro',
    'pix'            => 'Pix',
    'cartao_credito' => 'Cartão de crédito',
    'cartao_debito'  => 'Cartão de débito',
    'boleto'         => 'Boleto',
];
?>

<?php if ($mensagem): ?>
    <div class="alerta alerta-<?= $mensagem['tipo'] ?>">
        <?= icone($mensagem['tipo'] === 'sucesso' ? 'sucesso' : 'alerta') ?>
        <?= htmlspecialchars($mensagem['texto']) ?>
    </div>
<?php endif; ?>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Pedidos</h2>
        <a href="pedidos.php?acao=form" class="btn btn-primario">
            <?= icone('mais', 16) ?> Novo pedido
        </a>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="estado-vazio">
            <?= icone('pedidos', 32) ?>
            <p>Nenhum pedido registrado ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Forma de pagamento</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <?php $badge = $badgesPorStatus[$pedido['status']]; ?>
                    <tr>
                        <td>#<?= (int) $pedido['id'] ?></td>
                        <td><?= htmlspecialchars($pedido['cliente_nome'] ?? 'Balcão') ?></td>
                        <td><?= htmlspecialchars($formasPagamento[$pedido['forma_pagamento']] ?? '—') ?></td>
                        <td>R$ <?= number_format((float) $pedido['valor_total'], 2, ',', '.') ?></td>
                        <td><span class="badge <?= $badge['classe'] ?>"><?= $badge['texto'] ?></span></td>
                        <td><?= date('d/m/Y', strtotime($pedido['criado_em'])) ?></td>
                        <td class="acoes">
                            <a href="pedidos.php?acao=recibo&id=<?= $pedido['id'] ?>" title="Ver recibo">
                                <?= icone('relatorios', 16) ?>
                            </a>
                            <?php if ($pedido['status'] === 'pendente'): ?>
                                <a href="pedidos.php?acao=form&id=<?= $pedido['id'] ?>" title="Editar">
                                    <?= icone('editar', 16) ?>
                                </a>
                                <a href="pedidos.php?acao=status&id=<?= $pedido['id'] ?>&status=concluido"
                                   title="Concluir"
                                   onclick="return confirm('Concluir esta venda? O estoque dos itens será baixado.');">
                                    <?= icone('sucesso', 16) ?>
                                </a>
                                <a href="pedidos.php?acao=status&id=<?= $pedido['id'] ?>&status=cancelado"
                                   title="Cancelar"
                                   class="acao-excluir"
                                   onclick="return confirm('Cancelar este pedido?');">
                                    <?= icone('cancelar', 16) ?>
                                </a>
                            <?php elseif ($pedido['status'] === 'concluido'): ?>
                                <a href="pedidos.php?acao=status&id=<?= $pedido['id'] ?>&status=cancelado"
                                   title="Cancelar"
                                   class="acao-excluir"
                                   onclick="return confirm('Cancelar esta venda? O estoque baixado será devolvido.');">
                                    <?= icone('cancelar', 16) ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
