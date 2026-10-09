<?php
require_once __DIR__ . '/../../helpers/icones.php';

$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
?>

<?php if ($mensagem): ?>
    <div class="alerta alerta-<?= $mensagem['tipo'] ?>">
        <?= icone($mensagem['tipo'] === 'sucesso' ? 'sucesso' : 'alerta') ?>
        <?= htmlspecialchars($mensagem['texto']) ?>
    </div>
<?php endif; ?>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Clientes cadastrados</h2>
        <a href="clientes.php?acao=form" class="btn btn-primario">
            <?= icone('mais', 16) ?> Novo cliente
        </a>
    </div>

    <?php if (empty($clientes)): ?>
        <div class="estado-vazio">
            <?= icone('clientes', 32) ?>
            <p>Nenhum cliente cadastrado ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF/CNPJ</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['nome']) ?></td>
                        <td><?= htmlspecialchars($cliente['cpf'] ?? $cliente['cnpj'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($cliente['telefone'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($cliente['email'] ?? '—') ?></td>
                        <td class="acoes">
                            <a href="clientes.php?acao=form&id=<?= $cliente['id'] ?>" title="Editar">
                                <?= icone('editar', 16) ?>
                            </a>
                            <a href="clientes.php?acao=excluir&id=<?= $cliente['id'] ?>"
                               title="Excluir"
                               class="acao-excluir"
                               onclick="return confirm('Tem certeza que deseja excluir este cliente?');">
                                <?= icone('excluir', 16) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
