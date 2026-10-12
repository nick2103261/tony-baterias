<?php
require_once __DIR__ . '/../../helpers/icones.php';

$iconesPorTipo = [
    'usuario' => 'pessoa',
    'produto' => 'produtos',
    'pedido'  => 'pedidos',
];
?>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Atividades recentes</h2>
    </div>

    <?php if (empty($atividades)): ?>
        <div class="estado-vazio">
            <?= icone('atividades', 32) ?>
            <p>Nenhuma atividade registrada ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($atividades as $log): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($log['criado_em'])) ?></td>
                        <td><?= htmlspecialchars($log['usuario_nome']) ?></td>
                        <td>
                            <?= icone($iconesPorTipo[$log['tipo_registro']] ?? 'atividades', 14) ?>
                            <?= htmlspecialchars($log['acao']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>